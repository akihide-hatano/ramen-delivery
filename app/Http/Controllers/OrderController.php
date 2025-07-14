<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth; // Authファサードを追加
use Illuminate\Support\Facades\DB; // DBファサードを追加
use App\Models\Order; // Orderモデルを追加
use App\Models\OrderItem; // OrderItemモデルを追加
use App\Models\Product; // Productモデルを追加
use App\Models\Shop; // Shopモデルを追加

class OrderController extends Controller
{
    /**
     * 注文確認ページを表示する
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function checkout(Request $request) // ★★★このメソッドを追加します★★★
    {
        $cart = Session::get('cart', []);
        $cartShopId = Session::get('cart_shop_id');

        // カートが空か店舗IDがない場合はカートページにリダイレクト
        if (empty($cart) || !$cartShopId) {
            return redirect()->route('cart.index')->with('error', 'カートに商品がありません。');
        }

        $cartItems = [];
        $totalPrice = 0;

        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get();
        $shop = Shop::find($cartShopId);

        // 店舗が見つからない場合はカートをクリアしてリダイレクト
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

        return view('orders.index', compact('cartItems', 'totalPrice', 'shop'));
    }

    /**
     * 注文をデータベースに保存し、注文完了ページにリダイレクトする
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // リクエストデータのバリデーション
        $request->validate([
            'delivery_address' => 'required|string|max:255',
            'delivery_notes' => 'nullable|string|max:500',
        ]);

        $cart = Session::get('cart', []);
        $cartShopId = Session::get('cart_shop_id');

        if (empty($cart) || !$cartShopId) {
            return redirect()->route('cart.index')->with('error', 'カートに商品がありません。');
        }

        $totalPrice = 0;
        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id'); // IDをキーにして取得

        foreach ($cart as $productId => $quantity) {
            if (isset($products[$productId])) {
                $product = $products[$productId];
                $totalPrice += $product->price * $quantity;
            } else {
                // カートに存在しない商品IDがあった場合はエラーハンドリング
                Session::forget('cart');
                Session::forget('cart_shop_id');
                return redirect()->route('cart.index')->with('error', 'カートに含まれる商品の一部が見つかりませんでした。カートをクリアしました。');
            }
        }

        // トランザクションを開始
        DB::beginTransaction();

        try {
            // 注文データの保存
            $order = Order::create([
                'user_id' => Auth::id(), // ログインユーザーのID
                'shop_id' => $cartShopId,
                'total_price' => $totalPrice,
                'delivery_address' => $request->input('delivery_address'),
                'delivery_notes' => $request->input('delivery_notes'),
                'status' => 'pending', // 例: 'pending', 'completed', 'cancelled' など
            ]);

            // 注文商品データの保存
            foreach ($cart as $productId => $quantity) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price_at_purchase' => $products[$productId]->price, // 購入時の価格を保存
                ]);
            }

            // セッションからカート情報をクリア
            Session::forget('cart');
            Session::forget('cart_shop_id');

            DB::commit(); // トランザクションをコミット

            // 注文完了ページにリダイレクト
            return redirect()->route('orders.complete')->with('success', 'ご注文が完了しました！');

        } catch (\Exception $e) {
            DB::rollBack(); // エラーが発生した場合はロールバック
            // エラーログを記録
            \Log::error('Order creation failed: ' . $e->getMessage());
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
}