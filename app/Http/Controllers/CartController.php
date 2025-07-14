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
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'shop_id' => 'required|exists:shops,id',
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');
        $newShopId = $request->input('shop_id');

        $cart = Session::get('cart', []);
        $currentCartShopId = Session::get('cart_shop_id');

        // カートが空でない、かつ新しい商品が別の店舗のものである場合
        if (!empty($cart) && $currentCartShopId && $currentCartShopId != $newShopId) {
            Session::forget('cart');
            Session::forget('cart_shop_id');
            Session::flash('info', '別の店舗の商品を追加したため、カートをクリアしました。');
            $cart = [];
        }

        // カートが空の場合、または現在のカートに店舗IDが設定されていない場合、
        // または現在の店舗IDが新しい店舗IDと同じ場合、店舗IDをセッションに設定
        // ★★★ここを修正します★★★
        if (empty($cart) || is_null($currentCartShopId) || $currentCartShopId == $newShopId) {
            Session::put('cart_shop_id', $newShopId);
        }
        // ★★★修正ここまで★★★

        // カートに商品が存在すれば数量を更新、なければ追加
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        Session::put('cart', $cart);

        // dd([ // デバッグ用に追加していた場合は削除またはコメントアウトしてください
        //     'cart_after_processing' => Session::get('cart'),
        //     'cart_shop_id_after_processing' => Session::get('cart_shop_id'),
        // ]);

        return redirect()->route('cart.index')->with('success', '商品をカートに追加しました。');
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