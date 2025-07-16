<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Shop;

class CartController extends Controller
{
    /**
     * カートの内容を表示
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cart = Session::get('cart', []);
        $items = collect($cart)->map(function ($item) {
            $product = Product::with('shops')->find($item['product_id']); // 店舗情報もロード
            if ($product) {
                $item['product'] = $product;
                // 商品に紐づく最初の店舗名を取得（home.blade.phpの表示ロジックに合わせて）
                $item['shop_name'] = $product->shops->first()->name ?? '不明な店舗';
            }
            return $item;
        })->filter(function ($item) {
            return isset($item['product']); // 存在しない商品は除外
        });

        // 異なる店舗の商品がカートに混在しているかチェック
        $shopIds = $items->pluck('shop_id')->unique();
        $hasMixedShops = $shopIds->count() > 1;

        // カート内の商品が全て配達可能かチェック
        $allDeliverable = $items->every(function ($item) {
            return $item['product']->is_delivery;
        });

        // カート内の商品に配達不可なものが含まれているか
        $hasUndeliverableItem = $items->contains(function ($item) {
            return !$item['product']->is_delivery;
        });

        // 配達不可商品が含まれていて、かつ配達可能商品も混在している場合に警告メッセージ
        $warningMessage = '';
        if ($hasUndeliverableItem && !$allDeliverable) {
            $warningMessage = 'カートには配達できない商品が含まれています。配達注文を行う場合は、これらの商品を削除するか、店舗受け取りをご利用ください。';
        } elseif ($hasUndeliverableItem && $items->count() > 0 && $allDeliverable) {
            // カート内の全商品が配達不可の場合 (修正: この条件は、$allDeliverableがfalseの場合にのみ発動するべき)
            // 正しいロジック: 全てが配達不可であれば、$allDeliverableはfalseになる
            $warningMessage = 'カート内の商品は全て配達対象外です。店舗受け取りのみ可能です。';
        }


        return view('cart.index', compact('items', 'hasMixedShops', 'warningMessage'));
    }

    /**
     * カートに追加する商品を選択するページを表示
     * is_deliveryの商品を一覧で表示する
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $latitude = (float)$request->query('lat');
        $longitude = (float)$request->query('lon');
        $radiusKm = 20; // 検索半径（km）

        $nearbyShops = collect(); // 初期化
        $message = '位置情報を許可すると、お近くの店舗が表示されます。';

        $shops = Shop::all();

        // 位置情報がURLパラメータにある場合、近隣店舗を検索
        if ($latitude && $longitude) {
            $userLat = $latitude;
            $userLon = $longitude;

            $filteredShops = collect();

            foreach ($shops as $shop) {
                $shopLat = (float)$shop->lat;
                $shopLon = (float)$shop->lon;

                // 簡易的な距離計算 (Haversine formulaの簡略版)
                $theta = $userLon - $shopLon;
                $dist = sin(deg2rad($userLat)) * sin(deg2rad($shopLat)) + cos(deg2rad($userLat)) * cos(deg2rad($shopLat)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $meters = $dist * 60 * 1.1515 * 1609.344; // マイルをメートルに変換

                $shop->distance = $meters;
                if ($shop->distance <= $radiusKm * 1000) { // 半径（m）でフィルタリング
                    $filteredShops->push($shop);
                }
            }

            $nearbyShops = $filteredShops->sortBy('distance')->values();

            if ($nearbyShops->isNotEmpty()) {
                $message = '現在地から' . $radiusKm . 'km圏内に店舗が見つかりました。';
            } else {
                $message = '現在地から' . $radiusKm . 'km圏内に店舗は見つかりませんでした。';
            }
        } else {
            $message = '位置情報を許可すると、お近くの店舗が表示されます。';
        }

        // ユーザーが店舗を選択した場合
        $selectedShopId = $request->query('shop_id');
        $selectedShop = null;
        $deliverableProducts = collect(); // 空のコレクションで初期化

        if ($selectedShopId) {
            $selectedShop = Shop::find($selectedShopId);

            if ($selectedShop) {
                // 選択された店舗に紐づく配達可能な商品のみを取得
                $deliverableProducts = $selectedShop->products()
                                                    ->where('is_delivery', true)
                                                    ->get();
            } else {
                Session::flash('error', '選択された店舗が見つかりませんでした。');
            }
        }

        $mapsApiKey = env('Maps_API_KEY'); // Google Maps API Keyを取得

        // ビューに渡す変数
        return view('cart.add', compact('shops', 'nearbyShops', 'message', 'latitude', 'longitude', 'selectedShop', 'deliverableProducts', 'mapsApiKey'));
    }

    /**
     * カートに商品を追加（複数商品まとめて追加に対応）
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Request $request)
    {
        $itemsToAdd = $request->input('items', []);
        $selectedShopIdForCart = $request->input('selected_shop_id_for_cart'); // カートに追加する商品の店舗ID

        if (empty($itemsToAdd)) {
            return redirect()->back()->with('error', '追加する商品が選択されていません。');
        }

        if (!$selectedShopIdForCart) {
            return redirect()->back()->with('error', '商品を追加する店舗が指定されていません。');
        }

        $cart = Session::get('cart', []);
        $successCount = 0;

        // カートが空でない場合の店舗整合性チェック
        if (!empty($cart)) {
            $firstCartItem = reset($cart);
            // カート内の商品が紐づく店舗と、今追加しようとしている店舗が異なるか
            if ($firstCartItem['shop_id'] != $selectedShopIdForCart) {
                // カート内の商品と選択された店舗が異なる場合、クリア確認ページへリダイレクト
                Session::flash('previous_add_product_id', array_keys($itemsToAdd)[0] ?? null);
                Session::flash('previous_add_shop_id', $selectedShopIdForCart);
                Session::flash('previous_add_quantity', array_values($itemsToAdd)[0]['quantity'] ?? 1);
                return redirect()->route('cart.confirm-clear');
            }
        }


        foreach ($itemsToAdd as $productId => $itemData) {
            $quantity = $itemData['quantity'] ?? 1;

            if ($quantity <= 0) {
                continue;
            }

            $product = Product::find($productId);

            if (!$product) {
                Session::flash('error', ($request->session()->get('error', '')) . "商品ID: {$productId} が見つかりませんでした。<br>");
                continue;
            }

            // is_deliveryチェック
            if (!$product->is_delivery) {
                Session::flash('error', ($request->session()->get('error', '')) . "商品「{$product->name}」は配達対象外です。<br>");
                continue;
            }

            // 選択された店舗が、この商品に紐づいているか最終確認
            if (!$product->shops->contains($selectedShopIdForCart)) {
                Session::flash('error', ($request->session()->get('error', '')) . "商品「{$product->name}」は選択された店舗にはありません。<br>");
                continue;
            }

            // カートに商品を追加するロジック
            $found = false;
            foreach ($cart as $key => $item) {
                if ($item['product_id'] == $product->id && $item['shop_id'] == $selectedShopIdForCart) {
                    $cart[$key]['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $cart[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'shop_id' => $selectedShopIdForCart, // フォームから受け取った店舗IDを使用
                ];
            }
            $successCount++;
        }

        Session::put('cart', $cart);

        // ★★★ここから追加/修正★★★
        // カートに商品が追加された（または更新された）場合、cartShopIdをセッションに保存
        // カート内の商品はすべて同じshop_idを持つはずなので、最初の商品のshop_idを保存
        if (!empty($cart)) {
            $firstCartItem = reset($cart);
            Session::put('cartShopId', $firstCartItem['shop_id']);
        } else {
            // カートが空になった場合は、cartShopIdもクリアする（念のため）
            Session::forget('cartShopId');
        }
        // ★★★ここまで追加/修正★★★


        if ($successCount > 0) {
            return redirect()->route('cart.index')->with('success', "{$successCount}件の商品をカートに追加しました。");
        } else {
            return redirect()->back()->with('error', Session::get('error', '商品を追加できませんでした。'));
        }
    }


    /**
     * カート内の商品の数量を更新
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'shop_id' => 'required|exists:shops,id', // shop_idも必須とする
            'quantity' => 'required|integer|min:0',
        ]);

        $productId = $request->input('product_id');
        $shopId = $request->input('shop_id'); // shop_idを取得
        $quantity = $request->input('quantity');

        $cart = Session::get('cart', []);
        $updated = false; // 更新が実際に行われたかを示すフラグ

        foreach ($cart as $key => $item) {
            if ($item['product_id'] == $productId && $item['shop_id'] == $shopId) { // shop_idも比較条件に含める
                if ($quantity > 0) {
                    $cart[$key]['quantity'] = $quantity;
                    $updated = true;
                } else {
                    // 数量が0なら削除
                    unset($cart[$key]);
                    $updated = true;
                }
                break; // 見つかったらループを抜ける
            }
        }

        Session::put('cart', $cart);

        // ★★★ここから追加/修正★★★
        // カートが更新された場合、cartShopIdをセッションに保存
        // カート内の商品はすべて同じshop_idを持つはずなので、最初の商品のshop_idを保存
        if (!empty($cart)) {
            $firstCartItem = reset($cart);
            Session::put('cartShopId', $firstCartItem['shop_id']);
        } else {
            // カートが空になった場合は、cartShopIdもクリアする
            Session::forget('cartShopId');
        }
        // ★★★ここまで追加/修正★★★

        if ($updated) {
            return redirect()->route('cart.index')->with('success', 'カート数量を更新しました。');
        } else {
            return redirect()->route('cart.index')->with('error', 'カート内の商品が見つかりませんでした。');
        }
    }

    /**
     * カートから商品を削除
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'shop_id' => 'required|exists:shops,id', // shop_idも必須とする
        ]);

        $productId = $request->input('product_id');
        $shopId = $request->input('shop_id'); // shop_idを取得

        $cart = Session::get('cart', []);
        $removed = false; // 削除が実際に行われたかを示すフラグ

        foreach ($cart as $key => $item) {
            if ($item['product_id'] == $productId && $item['shop_id'] == $shopId) { // shop_idも比較条件に含める
                unset($cart[$key]);
                $removed = true;
                break; // 見つかったらループを抜ける
            }
        }

        Session::put('cart', $cart);

        // ★★★ここから追加/修正★★★
        // カートから商品が削除された後、cartShopIdを再評価
        if (!empty($cart)) {
            $firstCartItem = reset($cart);
            Session::put('cartShopId', $firstCartItem['shop_id']);
        } else {
            // カートが空になった場合は、cartShopIdもクリアする
            Session::forget('cartShopId');
        }
        // ★★★ここまで追加/修正★★★

        if ($removed) {
            return redirect()->route('cart.index')->with('success', '商品をカートから削除しました。');
        } else {
            return redirect()->route('cart.index')->with('error', 'カート内の商品が見つかりませんでした。');
        }
    }

    /**
     * カートをクリア
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        Session::forget('cart');
        // ★★★ここから追加★★★
        Session::forget('cartShopId'); // カートがクリアされたらショップIDもクリア
        // ★★★ここまで追加★★★
        return redirect()->route('cart.index')->with('success', 'カートが空になりました。');
    }

    /**
     * カートクリアの確認ページを表示
     *
     * @return \Illuminate\View\View
     */
    public function confirmClearCart()
    {
        // 以前のリクエストで追加しようとした商品の情報を取得
        $productId = Session::get('previous_add_product_id');
        $shopId = Session::get('previous_add_shop_id');
        $quantity = Session::get('previous_add_quantity');

        $product = null;
        $shop = null;

        if ($productId) {
            $product = Product::find($productId);
        }
        if ($shopId) {
            $shop = Shop::find($shopId);
        }

        return view('cart.confirm-clear', compact('product', 'shop', 'quantity'));
    }
}