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
        // その店舗に紐づく全ての商品を多対多リレーションシップ経由で取得
        $products = $shop->products() // Shopモデルのproducts()リレーションを使用
                            ->with('category')
                            ->orderBy('category_id') // カテゴリIDでソート
                            ->orderBy('name') // 商品名でソート
                            ->get();

        // カテゴリごとに商品をグループ化する（表示側で扱いやすくするため）
        $groupedProducts = $products->groupBy(function($product) {
            return $product->category ? $product->category->name : 'その他'; // カテゴリ名でグループ化
        });

        // ビューに店舗情報と商品データを渡して表示
        return view('products.index', compact('shop', 'groupedProducts'));
    }

    /**
     * 全体の商品一覧を表示 (共通商品と限定商品を分けて階層構造でグループ化)
     * ルート: /products (name: products.global_index)
     *
     * @return \Illuminate\View\View
     */
    public function globalIndex()
    {
        // 1. 全ての商品を取得（カテゴリ情報も一緒にロード）
        $allProducts = Product::with('category')->get();

        // 2. 商品を「限定」と「共通」に分ける
        $limitedProducts = $allProducts->filter(function ($product) {
            return str_contains($product->name, '限定！') || str_contains($product->name, '限定');
        });

        $commonProducts = $allProducts->reject(function ($product) {
            return str_contains($product->name, '限定！') || str_contains($product->name, '限定');
        });

        // 3. カテゴリを display_order 順に取得（グループ化の基準となるカテゴリ順）
        $mainCategories = Category::whereNull('parent_id')
                                    ->orderBy('display_order')
                                    ->get();

        $finalGroupedProducts = collect(); // 最終的にビューに渡すデータ

        // 「共通商品」のグループ化
        $finalGroupedProducts->put('共通商品', $this->groupProductsByHierarchy($commonProducts, $mainCategories));

        // 「限定商品」のグループ化
        $finalGroupedProducts->put('限定商品', $this->groupProductsByHierarchy($limitedProducts, $mainCategories));

        return view('products.global_index', compact('finalGroupedProducts'));
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
     * ヘルパーメソッド：指定された商品コレクションをカテゴリ階層に基づいてグループ化
     *
     * @param \Illuminate\Support\Collection $productsToGroup 処理対象の商品コレクション
     * @param \Illuminate\Support\Collection $mainCategories 最上位カテゴリのコレクション (display_order順)
     * @return \Illuminate\Support\Collection
     */
    private function groupProductsByHierarchy($productsToGroup, $mainCategories)
    {
        $grouped = collect();

        foreach ($mainCategories as $mainCategory) {
            // そのメインカテゴリの子カテゴリを display_order 順に取得
            $subCategories = Category::where('parent_id', $mainCategory->id)
                                    ->orderBy('display_order')
                                    ->get();

            // 子カテゴリがある場合
            if ($subCategories->isNotEmpty()) {
                $subCategoryGroups = collect();
                foreach ($subCategories as $subCategory) {
                    // さらに下位のカテゴリ（例: ビール、日本酒など）を取得
                    $nestedSubCategories = Category::where('parent_id', $subCategory->id)
                                                    ->orderBy('display_order')
                                                    ->get();

                    if ($nestedSubCategories->isNotEmpty()) {
                        // さらに深い階層のカテゴリがあれば、そのカテゴリ名でグループ化
                        $deepNestedGroups = collect();
                        foreach ($nestedSubCategories as $deepSubCategory) {
                            // このカテゴリに紐付く商品のみをフィルターし、名前順でソート
                            $productsInDeepSubCategory = $productsToGroup->where('category_id', $deepSubCategory->id)->sortBy('name');
                            if ($productsInDeepSubCategory->isNotEmpty()) {
                                $deepNestedGroups->put($deepSubCategory->name, $productsInDeepSubCategory);
                            }
                        }
                        // 中間カテゴリ名の下に、さらに深いカテゴリのグループを追加
                        if ($deepNestedGroups->isNotEmpty()) {
                             $subCategoryGroups->put($subCategory->name, $deepNestedGroups);
                        }
                    } else {
                        // 最下層のカテゴリ（商品が直接紐付いているカテゴリ）の場合
                        // このカテゴリに紐付く商品のみをフィルターし、名前順でソート
                        $productsInSubCategory = $productsToGroup->where('category_id', $subCategory->id)->sortBy('name');
                        if ($productsInSubCategory->isNotEmpty()) {
                            $subCategoryGroups->put($subCategory->name, $productsInSubCategory);
                        }
                    }
                }
                // サブカテゴリグループがあれば、メインカテゴリの下に追加
                if ($subCategoryGroups->isNotEmpty()) {
                    $grouped->put($mainCategory->name, $subCategoryGroups);
                }
            } else {
                // 最上位カテゴリに直接商品が紐付いている場合
                // このカテゴリに紐付く商品のみをフィルターし、名前順でソート
                $directProducts = $productsToGroup->where('category_id', $mainCategory->id)->sortBy('name');
                if ($directProducts->isNotEmpty()) {
                    $grouped->put($mainCategory->name, $directProducts);
                }
            }
        }
        return $grouped;
    }

    // getAllChildCategoryIds ヘルパーメソッドは、この新しいロジックでは不要になったため削除しても問題ありません。
    // private function getAllChildCategoryIds($categoryId) { ... }
}