<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Productモデルをインポート
use App\Models\Category; // Categoryモデルをインポート
use App\Models\Shop; // Shopモデルをインポート

class ProductController extends Controller
{
    /**
     * 指定された店舗の商品一覧を表示
     * ルート: /shops/{shop}/products (name: shops.products.index)
     *
     * @param  \App\Models\Shop  $shop // Implicit Model Binding
     * @return \Illuminate\View\View
     */
    public function index(Shop $shop)
    {
        // その店舗に紐づく全ての商品を取得
        $products = Product::where('shop_id', $shop->id)
                            ->with('category') // カテゴリ情報も一緒にロード
                            ->orderBy('category_id') // カテゴリごとに表示をまとめたい場合などに便利
                            ->orderBy('price') // 価格順も追加
                            ->get();

        // カテゴリごとに商品をグループ化する（表示側で扱いやすくするため）
        $groupedProducts = $products->groupBy(function($product) {
            return $product->category ? $product->category->name : 'その他'; // カテゴリ名でグループ化
        });

        // ビューに店舗情報と商品データを渡して表示
        return view('products.index', compact('shop', 'groupedProducts'));
    }

    /**
     * 全体の商品一覧を表示
     * ルート: /products (name: products.global_index)
     *
     * @return \Illuminate\View\View
     */
    public function globalIndex() // このメソッドを追加
    {
        // 全ての商品を取得
        $products = Product::with('category') // カテゴリ情報も一緒にロード
                            ->orderBy('category_id') // カテゴリごとに表示をまとめる
                            ->orderBy('price')
                            ->get();

        // カテゴリごとに商品をグループ化
        $groupedProducts = $products->groupBy(function($product) {
            return $product->category ? $product->category->name : 'その他';
        });


        // ビューに商品データを渡して表示
        return view('products.global_index', compact('groupedProducts'));
    }

    /**
     * 個別商品詳細を表示
     * ルート: /products/{product} (name: products.show)
     *
     * @param  \App\Models\Product  $product // Implicit Model Binding
     * @return \Illuminate\View\View
     */
    public function show(Product $product) // このメソッドも修正 (int $productId から Product $product へ)
    {
        return view('products.show', compact('product'));
    }
}