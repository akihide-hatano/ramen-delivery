<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Support\Facades\Log; // Logファサードを追加
use Carbon\Carbon; // Carbonライブラリをuse

class OrderController extends Controller
{
    /**
     * 注文確認ページを表示する
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function checkout(Request $request)
    {
        $cart = Session::get('cart', []);
        $cartShopId = Session::get('cart_shop_id');

        if (empty($cart) || !$cartShopId) {
            return redirect()->route('cart.index')->with('error', 'カートに商品がありません。');
        }

        $cartItems = [];
        $totalPrice = 0;

        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get();
        $shop = Shop::find($cartShopId);

        if (!$shop) {
            Session::forget('cart');
            Session::forget('cart_shop_id');
            return redirect()->route('cart.index')->with('error', 'カートに紐づく店舗が見つかりませんでした。カートをクリアしました。');
        }

        foreach ($products as $product) {
            $quantity = $cart[$product->id];
            $subtotal = $product->price * $quantity;
            $totalPrice += $subtotal;

            $cartItems[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
        }

        // 配達エリアのリストをconfigから取得
        $deliveryZones = config('delivery.delivery_zones');

        // デフォルトの予測配達時間を計算 (最初のエリアを仮定して計算)
        $estimatedDeliveryTimeMinutes = null;
        if (!empty($deliveryZones) && $shop->lat && $shop->lon) {
            // 例として最初のエリアの緯度経度を使って初期予測を出す
            $firstZone = reset($deliveryZones); // 配列の最初の要素を取得
            $estimatedDeliveryTimeMinutes = $this->calculateEstimatedDeliveryTime(
                $shop->lat, $shop->lon,
                $firstZone['latitude'], $firstZone['longitude']
            );
        }
        return view('orders.index', compact('cartItems', 'totalPrice', 'shop', 'deliveryZones', 'estimatedDeliveryTimeMinutes'));
    }

    /**
     * 注文をデータベースに保存し、注文完了ページにリダイレクトする
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'delivery_address' => 'required|string|max:255',
            'delivery_notes' => 'nullable|string|max:500',
            'delivery_zone_name' => 'required|string|in:' . implode(',', array_keys(config('delivery.delivery_zones'))), // 配達エリア名を追加
        ]);

        $cart = Session::get('cart', []);
        $cartShopId = Session::get('cart_shop_id');

        if (empty($cart) || !$cartShopId) {
            return redirect()->route('cart.index')->with('error', 'カートに商品がありません。');
        }

        $totalPrice = 0;
        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($cart as $productId => $quantity) {
            if (isset($products[$productId])) {
                $product = $products[$productId];
                $totalPrice += $product->price * $quantity;
            } else {
                Session::forget('cart');
                Session::forget('cart_shop_id');
                return redirect()->route('cart.index')->with('error', 'カートに含まれる商品の一部が見つかりませんでした。カートをクリアしました。');
            }
        }

        $shop = Shop::find($cartShopId);
        if (!$shop) {
            // ここでのエラーは通常発生しないはずだが、念のため
            return redirect()->route('cart.index')->with('error', '注文店舗が見つかりませんでした。');
        }

        // 選択された配達エリアの緯度・経度を取得
        $selectedDeliveryZoneName = $request->input('delivery_zone_name');
        $deliveryZones = config('delivery.delivery_zones');
        $selectedZoneCoords = $deliveryZones[$selectedDeliveryZoneName] ?? null;

        $estimatedDeliveryTimeMinutes = null;
        if ($selectedZoneCoords && $shop->lat && $shop->lon) {
            $estimatedDeliveryTimeMinutes = $this->calculateEstimatedDeliveryTime(
                $shop->lat, $shop->lon,
                $selectedZoneCoords['latitude'], $selectedZoneCoords['longitude']
            );
        }

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => Auth::id(),
                'shop_id' => $cartShopId,
                'total_amount' => $totalPrice,
                'delivery_address' => $request->input('delivery_address'),
                'delivery_notes' => $request->input('delivery_notes'),
                'status' => 'pending',
                'delivery_zone_name' => $selectedDeliveryZoneName, // ★追加: 配達エリア名
                'estimated_delivery_time_minutes' => $estimatedDeliveryTimeMinutes, // ★追加: 予測配達時間
                // delivery_lat, delivery_lon は今回は使用しないため、DBから削除済みであればOK
            ]);

            foreach ($cart as $productId => $quantity) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $products[$productId]->price,
                ]);
            }

            Session::forget('cart');
            Session::forget('cart_shop_id');

            DB::commit();

            return redirect()->route('orders.complete')->with('success', 'ご注文が完了しました！');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());
            return redirect()->route('cart.index')->with('error', '注文処理中にエラーが発生しました。もう一度お試しください。');
        }
    }

    /**
     * 注文完了ページを表示する
     *
     * @return \Illuminate\View\View
     */
    public function complete()
    {
        return view('orders.complete');
    }

    /**
     * 予測配達時間を計算するヘルパーメソッド
     *
     * @param float $shopLat 店舗の緯度
     * @param float $shopLon 店舗の経度
     * @param float $destLat 配達先の緯度
     * @param float $destLon 配達先の経度
     * @return int 予測配達時間 (分)
     */
    private function calculateEstimatedDeliveryTime($shopLat, $shopLon, $destLat, $destLon): int
    {
        $basePreparationTime = config('delivery.base_preparation_time_minutes', 20);
        $deliverySpeedPerKm = config('delivery.delivery_speed_minutes_per_km', 3);
        $peakHours = config('delivery.peak_hours', []);
        $peakSurcharge = config('delivery.peak_surcharge_minutes', 15);
        $bufferMin = config('delivery.buffer_minutes_min', 5);
        $bufferMax = config('delivery.buffer_minutes_max', 15);

        // 距離を計算 (Haversine formula)
        $distanceKm = $this->calculateDistance($shopLat, $shopLon, $destLat, $destLon);

        $estimatedTime = $basePreparationTime + ($distanceKm * $deliverySpeedPerKm);

        // ピーク時間帯の判定と加算
        $now = Carbon::now();
        foreach ($peakHours as $period) {
            list($start, $end) = explode('-', $period);
            $startTime = Carbon::parse($start);
            $endTime = Carbon::parse($end);

            // 日付を考慮せず時間帯のみで判定
            if ($now->between($startTime, $endTime, true)) {
                $estimatedTime += $peakSurcharge;
                break; // 複数のピーク時間帯に重なる場合は最初のものだけ適用
            }
        }

        // ランダムなバッファを加算
        $estimatedTime += rand($bufferMin, $bufferMax);

        return (int) round($estimatedTime);
    }

    /**
     * 2点間の距離をkmで計算する（Haversine formula）
     *
     * @param float $lat1 緯度1
     * @param float $lon1 経度1
     * @param float $lat2 緯度2
     * @param float $lon2 経度2
     * @return float 距離 (km)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371; // 地球の半径 (km)

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c; // 距離 (km)
        return $distance;
    }
}