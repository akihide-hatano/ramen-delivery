<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Productモデルを使用するため
use App\Models\Shop;    // Shopモデルを使用するため
use Illuminate\Support\Facades\Session; // Sessionファサードを使用するため
use App\Models\Order; // Orderモデルを使用するため (今後使用)
use App\Models\OrderItem; // OrderItemモデルを使用するため (今後使用)
use Illuminate\Support\Facades\Auth; // 認証済みユーザー情報を取得するため

class OrderController extends Controller
{
    /**
     * 注文確認（配送先入力）ページを表示
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $cart = Session::get('cart', []);
        $cartShopId = Session::get('cart_shop_id');

        // カートが空の場合、またはカートに紐づく店舗がない場合は、カートページにリダイレクト
        if (empty($cart) || !$cartShopId) {
            return redirect()->route('cart.index')->with('error', 'カートに商品がありません。');
        }

        $cartItems = [];
        $totalPrice = 0;
        $shop = Shop::find($cartShopId); // カートが紐づく店舗情報を取得

        // カート内の商品IDリストを取得
        $productIds = array_keys($cart);
        // データベースから商品情報を取得
        $products = Product::whereIn('id', $productIds)->get();

        // カートの内容を整形し、合計金額を計算
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

        // 認証済みユーザーのデフォルト住所などを取得することも可能
        // $user = Auth::user();
        // $defaultAddress = $user->address ?? ''; // 例: ユーザーモデルにaddressカラムがある場合

        return view('orders.index', compact('cartItems', 'totalPrice', 'shop'));
    }

    // 今後、注文を保存するstoreメソッドなどをここに追加していきます
}