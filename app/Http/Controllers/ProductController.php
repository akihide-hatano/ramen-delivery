<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Productモデルをインポート
use App\Models\Category; // Categoryモデルをインポート（必要に応じて）
use App\Models\Shop; // Shopモデルをインポート

class ProductController extends Controller
{
    /**
     * 指定された店舗の商品一覧を表示
     *
     * @param  int  $shopId
     * @return \Illuminate\View\View
     */
    public function index(int $shopId)
    {
        // 店舗情報を取得（存在しない場合は404エラー）
        $shop = Shop::findOrFail($shopId);

        // その店舗に紐づく全ての商品を取得
        // カテゴリ名でソートして表示したい場合などを考慮し、with('category')でリレーションをロード
        $products = Product::where('shop_id', $shopId)
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
     * 個別商品詳細を表示する場合のメソッド（もし必要であれば）
     *
     * @param  int  $productId
     * @return \Illuminate\View\View
     */
    // public function show(int $productId)
    // {
    //     $product = Product::findOrFail($productId);
    //     return view('products.show', compact('product'));
    // }
}