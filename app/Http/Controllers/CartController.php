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
            // カート内の全商品が配達不可の場合
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
    public function create()
    {
        // 配達可能な商品のみを取得
        $deliverableProducts = Product::where('is_delivery', true)
                                    ->with('shops') // 店舗情報も必要ならロード
                                    ->get();

        return view('cart.add', compact('deliverableProducts'));
    }

    /**
     * カートに商品を追加（複数商品まとめて追加に対応）
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Request $request)
    {
        // 'items' 配列が送信されているか確認
        $itemsToAdd = $request->input('items', []);

        if (empty($itemsToAdd)) {
            return redirect()->back()->with('error', '追加する商品が選択されていません。');
        }

        $cart = Session::get('cart', []);
        $successCount = 0; // 正常に追加された商品の数

        foreach ($itemsToAdd as $productId => $itemData) {
            $quantity = $itemData['quantity'] ?? 1;
            $selectedShopId = $itemData['shop_id'] ?? null;

            if ($quantity <= 0) {
                continue; // 数量が0以下の商品はスキップ
            }

            $product = Product::find($productId);

            if (!$product) {
                Session::flash('error', ($request->session()->get('error', '')) . "商品ID: {$productId} が見つかりませんでした。<br>");
                continue; // 次の商品へ
            }

            // is_deliveryチェック (以前の単一商品追加時と同様)
            if (!$product->is_delivery) {
                Session::flash('error', ($request->session()->get('error', '')) . "商品「{$product->name}」は配達対象外です。<br>");
                continue; // 次の商品へ
            }

            // 選択された店舗が有効か、商品に紐づいているか確認
            if (!$selectedShopId || !$product->shops->contains($selectedShopId)) {
                Session::flash('error', ($request->session()->get('error', '')) . "商品「{$product->name}」に有効な店舗が選択されていません。<br>");
                continue; // 次の商品へ
            }

            // カートが空でない場合の店舗整合性チェック
            if (!empty($cart)) {
                $firstCartItem = reset($cart);
                $firstProductInCart = Product::find($firstCartItem['product_id']);
                // カート内の商品が紐づく店舗と、今追加しようとしている商品の店舗が異なるか
                // かつ、その商品の店舗が、今追加しようとしている店舗に含まれていない場合
                if ($firstProductInCart && !$firstProductInCart->shops->contains($selectedShopId)) {
                    Session::flash('error', 'カートには他の店舗の商品が含まれています。異なる店舗の商品を同時に追加することはできません。カートをクリアしてから再度お試しください。');
                    return redirect()->route('cart.index');
                }
            }

            // カートに商品を追加するロジック
            $found = false;
            foreach ($cart as $key => $item) {
                if ($item['product_id'] == $product->id && $item['shop_id'] == $selectedShopId) {
                    $cart[$key]['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $cart[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'shop_id' => $selectedShopId,
                ];
            }
            $successCount++;
        }

        Session::put('cart', $cart);

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

        foreach ($cart as $key => $item) {
            if ($item['product_id'] == $productId && $item['shop_id'] == $shopId) { // shop_idも比較条件に含める
                if ($quantity > 0) {
                    $cart[$key]['quantity'] = $quantity;
                    Session::put('cart', $cart);
                    return redirect()->route('cart.index')->with('success', 'カート数量を更新しました。');
                } else {
                    // 数量が0なら削除
                    unset($cart[$key]);
                    Session::put('cart', $cart);
                    return redirect()->route('cart.index')->with('success', '商品をカートから削除しました。');
                }
            }
        }

        return redirect()->route('cart.index')->with('error', 'カート内の商品が見つかりませんでした。');
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

        foreach ($cart as $key => $item) {
            if ($item['product_id'] == $productId && $item['shop_id'] == $shopId) { // shop_idも比較条件に含める
                unset($cart[$key]);
                Session::put('cart', $cart);
                return redirect()->route('cart.index')->with('success', '商品をカートから削除しました。');
            }
        }

        return redirect()->route('cart.index')->with('error', 'カート内の商品が見つかりませんでした。');
    }

    /**
     * カートをクリア
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        Session::forget('cart');
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