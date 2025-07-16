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
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config; // Configファサードを追加

class OrderController extends Controller
{
    /**
     * 注文情報入力ページを表示 (旧 checkout メソッド)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create(Request $request) // メソッド名を create に変更
    {
        $cart = Session::get('cart', []);
        // CartController@add で cartShopId がセットされることを想定
        $cartShopId = Session::get('cartShopId'); // ★ここを 'cartShopId' に統一

        // dd($cartShopId); // デバッグ用: ここでは"7"が表示されるはず

        // `dd` の引数も修正して、正しい `cartShopId` 変数を参照させる
        if (empty($cart) || !$cartShopId) {
            // dd('Redirecting: Cart empty or shop ID missing', ['cart' => $cart, 'cartShopId' => $cartShopId, 'session_all' => Session::all()]); // デバッグ用
            return redirect()->route('cart.index')->with('error', 'カートに商品がありません。');
        }

        $items = collect($cart)->map(function ($quantity, $productId) {
            $product = Product::with('shops')->find($productId);
            if ($product) {
                return [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'product' => $product,
                    'shop_name' => $product->shops->first()->name ?? '不明な店舗',
                    'subtotal' => $product->price * $quantity,
                    'shop_id' => $product->shops->first()->id ?? null,
                ];
            }
            return null;
        })->filter()->values();

        // カート内の商品が全て同じ店舗からのものか最終確認
        $shopIdsInCart = $items->pluck('shop_id')->unique();
        if ($shopIdsInCart->count() > 1) {
            return redirect()->route('cart.index')->with('error', 'カートには複数の店舗の商品が含まれています。注文を完了するには、いずれかの店舗の商品を削除してください。');
        }
        if ($shopIdsInCart->first() != $cartShopId) {
             // セッションのcartShopIdと実際のカート内容が異なる場合の処理
             Session::forget('cart');
             Session::forget('cartShopId'); // ★ここを 'cartShopId' に統一
             return redirect()->route('cart.index')->with('error', 'カート情報が不正です。カートをクリアしました。');
        }

        // カート内の商品が全て配達可能か最終確認
        $hasUndeliverableItem = $items->contains(function ($item) {
            return !$item['product']->is_delivery;
        });

        if ($hasUndeliverableItem) {
            return redirect()->route('cart.index')->with('error', 'カートには配達対象外の商品が含まれています。店舗受け取りをご希望の場合は、カートを調整してください。');
        }

        $totalPrice = $items->sum('subtotal');
        $deliveryFee = 500; // 仮の配送料
        $grandTotal = $totalPrice + $deliveryFee;

        // 注文する店舗の情報を取得
        $shop = Shop::find($cartShopId);

        if (!$shop) {
            Session::forget('cart');
            Session::forget('cartShopId'); // ★ここを 'cartShopId' に統一
            return redirect()->route('cart.index')->with('error', 'カートに紐づく店舗が見つかりませんでした。カートをクリアしました。');
        }

        // 認証済みユーザーのデフォルト住所と電話番号を取得
        $user = Auth::user();
        $defaultAddress = $user->address ?? '';
        $defaultPhoneNumber = $user->phone_number ?? '';

        // configファイルから配達エリアと時間帯オプションを取得
        $deliveryZones = Config::get('delivery.delivery_zones');
        $deliveryTimeOptions = Config::get('delivery.delivery_time_slots');
        $paymentMethodOptions = [
            'cash' => '現金払い',
            'credit_card' => 'クレジットカード',
        ];
        $mapsApiKey = env('Maps_API_KEY');

        $estimatedDeliveryTimeMinutes = null;

        return view('orders.create', compact(
            'items',
            'shop',
            'totalPrice',
            'deliveryFee',
            'grandTotal',
            'defaultAddress',
            'defaultPhoneNumber',
            'deliveryTimeOptions',
            'paymentMethodOptions',
            'deliveryZones',
            'mapsApiKey',
            'estimatedDeliveryTimeMinutes'
        ));
    }

    /**
     * 注文をデータベースに保存し、注文完了ページにリダイレクトする
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'delivery_address' => 'required|string|max:255',
            'delivery_phone' => 'required|string|max:20',
            'delivery_notes' => 'nullable|string|max:1000',
            'delivery_zone_name' => 'required|string|in:' . implode(',', array_keys(config('delivery.delivery_zones'))),
            'desired_delivery_time_slot' => 'nullable|string|in:' . implode(',', array_keys(config('delivery.delivery_time_slots'))),
            'payment_method' => 'required|string|in:cash,credit_card',
        ]);

        $cart = Session::get('cart', []);
        $cartShopId = Session::get('cartShopId'); // ★ここを 'cartShopId' に統一

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
                Session::forget('cartShopId'); // ★ここを 'cartShopId' に統一
                return redirect()->route('cart.index')->with('error', 'カートに含まれる商品の一部が見つかりませんでした。カートをクリアしました。');
            }
        }

        $shop = Shop::find($cartShopId);

        if (!$shop) {
            return redirect()->route('cart.index')->with('error', '注文店舗が見つかりませんでした。');
        }

        $deliveryFee = 500;
        $grandTotal = $totalPrice + $deliveryFee;

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => Auth::id(),
                'shop_id' => $cartShopId,
                'delivery_address' => $request->input('delivery_address'),
                'delivery_phone' => $request->input('delivery_phone'),
                'delivery_zone_name' => $request->input('delivery_zone_name'),
                'desired_delivery_time_slot' => $request->input('desired_delivery_time_slot'),
                'delivery_notes' => $request->input('delivery_notes'),
                'total_price' => $totalPrice,
                'delivery_fee' => $deliveryFee,
                'grand_total' => $grandTotal,
                'payment_method' => $request->input('payment_method'),
                'status' => 'pending',
            ]);

            foreach ($cart as $productId => $quantity) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $products[$productId]->price,
                    'subtotal' => $products[$productId]->price * $quantity,
                ]);
            }

            Session::forget('cart');
            Session::forget('cartShopId'); // ★ここを 'cartShopId' に統一

            DB::commit();

            return redirect()->route('orders.complete')->with('success', 'ご注文が完了しました！');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', '注文処理中にエラーが発生しました。もう一度お試しください。');
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
            list($startStr, $endStr) = explode('-', $period);
            $startTime = Carbon::parse($startStr);
            $endTime = Carbon::parse($endStr);

            $currentMinutes = $now->hour * 60 + $now->minute;
            $startMinutes = $startTime->hour * 60 + $startTime->minute;
            $endMinutes = $endTime->hour * 60 + $endTime->minute;

            if ($startMinutes < $endMinutes) {
                if ($currentMinutes >= $startMinutes && $currentMinutes < $endMinutes) {
                    $estimatedTime += $peakSurcharge;
                    break;
                }
            } else {
                if ($currentMinutes >= $startMinutes || $currentMinutes < $endMinutes) {
                    $estimatedTime += $peakSurcharge;
                    break;
                }
            }
        }

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
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;
        return $distance;
    }
}