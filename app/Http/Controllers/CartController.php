<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * カート内容を表示
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $cart = Session::get('cart', []);
        $cartShopId = Session::get('cart_shop_id');

        $cartItems = [];
        $totalPrice = 0;
        $shop = null;

        if (!empty($cart) && $cartShopId) {
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
        }
        // dd($cart, $cartShopId); // デバッグ用に追加していた場合は削除またはコメントアウトしてください

        return view('cart.index', compact('cartItems', 'totalPrice', 'shop'));
    }

    /**
     * カートに商品を追加
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Request $request)
    {
        // shop_id はここでは必須にせず、後続のロジックで判断します。
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        // 商品とその店舗情報を取得
        $product = Product::with('shop')->find($productId);

        if (!$product) {
            return back()->with('error', '商品が見つかりませんでした。');
        }

        $cart = Session::get('cart', []);
        $currentCartShopId = Session::get('cart_shop_id'); // 現在カートに入っている商品の店舗ID

        // Case 1: カートが空の場合、または現在のカートに店舗IDが設定されていない場合
        //         この場合は、まず店舗選択ページへリダイレクトします。
        if (empty($cart) || $currentCartShopId === null) {
            // カートに追加する商品を一時的にセッションに保存
            Session::put('pending_product_id', $productId);
            Session::put('pending_quantity', $quantity);

            // 店舗選択ページへリダイレクト
            return redirect()->route('orders.choose-shop-for-product', $productId);
        }
        // Case 2: 現在カートに入っている商品と同じ店舗の商品を追加する場合
        elseif ((string)$currentCartShopId === (string)$product->shop_id) {
            $cart[$productId] = ($cart[$productId] ?? 0) + $quantity;
            Session::put('cart', $cart);
            // Session::put('cart_shop_id', $product->shop_id); // 既に設定済みなので不要
            return back()->with('success', $product->name . 'をカートに追加しました。');
        }
        // Case 3: 異なる店舗の商品を追加しようとした場合
        else {
            // ユーザーに確認を促すために必要な情報をフラッシュセッションに保存し、確認ページへリダイレクト
            Session::flash('requested_product_id', $productId);
            Session::flash('requested_quantity', $quantity);
            Session::flash('new_shop_name', $product->shop->name);
            // 現在のカートの店舗名を取得（存在しない場合は「不明な店舗」）
            $currentShop = Shop::find($currentCartShopId);
            Session::flash('current_shop_name', $currentShop ? $currentShop->name : '不明な店舗');

            return redirect()->route('cart.confirm-clear')->with('error', '現在、別の店舗の商品がカートに入っています。カートをクリアして' . $product->shop->name . 'の商品を追加しますか？');
        }
    }

    /**
     * カートの商品数量を更新
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0', // 0の場合は削除
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        $cart = Session::get('cart', []);

        if (isset($cart[$productId])) {
            if ($quantity > 0) {
                $cart[$productId] = $quantity; // 数量を更新
                Session::put('cart', $cart);
                return redirect()->route('cart.index')->with('success', 'カートの商品数量を更新しました。');
            } else {
                // 数量が0以下の場合は商品をカートから削除
                unset($cart[$productId]);
                Session::put('cart', $cart);
                // カートが空になったら店舗IDもクリア
                if (empty($cart)) {
                    Session::forget('cart_shop_id');
                }
                return redirect()->route('cart.index')->with('success', 'カートから商品を削除しました。');
            }
        }

        return redirect()->route('cart.index')->with('error', 'カートに商品が見つかりませんでした。');
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
        ]);

        $productId = $request->input('product_id');

        $cart = Session::get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]); // 商品をカートから削除
            Session::put('cart', $cart);
            // カートが空になったら店舗IDもクリア
            if (empty($cart)) {
                Session::forget('cart_shop_id');
            }
            return redirect()->route('cart.index')->with('success', 'カートから商品を削除しました。');
        }

        return redirect()->route('cart.index')->with('error', 'カートに商品が見つかりませんでした。');
    }

    /**
     * カートをクリア
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        Session::forget('cart'); // カートデータをセッションから削除
        Session::forget('cart_shop_id'); // 店舗IDもクリア
        return redirect()->route('cart.index')->with('success', 'カートをクリアしました。');
    }
}