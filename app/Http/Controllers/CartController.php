<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
/**
     * カート内容を表示
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // セッションからカートデータを取得
        // カートデータは ['product_id' => quantity, ...] の形式
        $cart = Session::get('cart', []);

        $cartItems = [];
        $totalPrice = 0;

        if (!empty($cart)) {
            // カート内の商品IDを配列で取得
            $productIds = array_keys($cart);

            // データベースから商品情報を取得
            $products = Product::whereIn('id', $productIds)->get();

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

        return view('cart.index', compact('cartItems', 'totalPrice'));
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
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        // セッションから現在のカートデータを取得
        $cart = Session::get('cart', []);

        // カートに商品が存在すれば数量を更新、なければ追加
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        // 更新されたカートデータをセッションに保存
        Session::put('cart', $cart);

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
        return redirect()->route('cart.index')->with('success', 'カートをクリアしました。');
    }
}
