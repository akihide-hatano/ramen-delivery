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
        // ★ここを修正しました★
        // その店舗に紐づく全ての商品を多対多リレーションシップ経由で取得
        // with('category') でカテゴリ情報も一緒にロード
        // orderBy('category_id') でカテゴリごとに表示をまとめ、orderBy('name') で商品名をソート
        $products = $shop->products() // Shopモデルのproducts()リレーションを使用
                            ->with('category')
                            ->orderBy('category_id') // カテゴリIDでソート
                            ->orderBy('name') // 商品名でソート
                            ->get();

        // カテゴリごとに商品をグループ化する（表示側で扱いやすくするため）
        // ここでは、カテゴリの表示順序は考慮せず、単純にカテゴリ名でグループ化
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
    public function globalIndex() // このメソッドを編集
    {
        // ★ここを修正しました★
        // カテゴリを display_order 順に取得し、その順序で商品をグループ化
        // まず、親カテゴリ（parent_idがnull）を display_order 順に取得
        $mainCategories = Category::whereNull('parent_id')
                                    ->orderBy('display_order')
                                    ->get();

        $groupedProducts = collect(); // 空のコレクションで初期化

        foreach ($mainCategories as $mainCategory) {
            // そのメインカテゴリに直接紐付く商品を取得
            $directProducts = Product::where('category_id', $mainCategory->id)->get();

            // そのメインカテゴリの子孫カテゴリのIDを全て取得
            $allChildCategoryIds = $this->getAllChildCategoryIds($mainCategory->id);

            // 子孫カテゴリに紐付く商品を取得
            $childProducts = collect();
            if ($allChildCategoryIds->isNotEmpty()) {
                $childProducts = Product::whereIn('category_id', $allChildCategoryIds)->get();
            }

            // 直接の商品と子孫カテゴリの商品を結合
            $combinedProducts = $directProducts->concat($childProducts);

            // 商品があれば、メインカテゴリ名でグループに追加
            if ($combinedProducts->isNotEmpty()) {
                // 必要であれば、ここで結合した商品をさらにソート（例：商品名順）
                $groupedProducts->put($mainCategory->name, $combinedProducts->sortBy('name'));
            }
        }

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
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * ヘルパーメソッド：指定したカテゴリIDの全ての子孫カテゴリIDを取得
     *
     * @param int $categoryId
     * @return \Illuminate\Support\Collection
     */
    private function getAllChildCategoryIds($categoryId)
    {
        $childIds = collect();
        $directChildren = Category::where('parent_id', $categoryId)->get();

        foreach ($directChildren as $child) {
            $childIds->push($child->id);
            $childIds = $childIds->concat($this->getAllChildCategoryIds($child->id)); // 再帰的に子孫を取得
        }
        return $childIds->unique(); // 重複を除去
    }
}