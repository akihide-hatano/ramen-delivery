<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Shop;

class ProductController extends Controller
{
    // ... (index メソッドは変更なし) ...

    /**
     * 全体の商品一覧を表示 (共通商品と限定商品を分けて階層構造でグループ化)
     * ルート: /products (name: products.global_index)
     *
     * @return \Illuminate\View\View
     */
    public function globalIndex()
    {
        // 1. 全ての商品を取得（カテゴリ情報も一緒にロード）
        // ここも with(['category.parent']) に変更
        $allProducts = Product::with(['category.parent'])->get();

        // 2. 商品を「限定」と「共通」に分ける
        $limitedProducts = $allProducts->filter(function ($product) {
            return $product->is_limited;
        });

        $commonProducts = $allProducts->reject(function ($product) {
            return $product->is_limited;
        });

        // 3. カテゴリを display_order 順に取得（グループ化の基準となるカテゴリ順）
        $mainCategories = Category::whereNull('parent_id')
                                    ->orderBy('display_order')
                                    ->get();

        $finalGroupedProducts = collect();

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
        // ★ここを修正しました：category.parent を eager load する★
        $product->load('category.parent');
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
            // ここも with(['products', 'parent']) に変更
            $subCategories = Category::where('parent_id', $mainCategory->id)
                                    ->orderBy('display_order')
                                    ->with(['products', 'parent']) // ★親カテゴリもロード★
                                    ->get();

            if ($subCategories->isNotEmpty()) {
                $subCategoryGroups = collect();
                foreach ($subCategories as $subCategory) {
                    // ここも with(['products', 'parent']) に変更
                    $nestedSubCategories = Category::where('parent_id', $subCategory->id)
                                                    ->orderBy('display_order')
                                                    ->with(['products', 'parent']) // ★親カテゴリもロード★
                                                    ->get();

                    if ($nestedSubCategories->isNotEmpty()) {
                        $deepNestedGroups = collect();
                        foreach ($nestedSubCategories as $deepSubCategory) {
                            $productsInDeepSubCategory = $productsToGroup->where('category_id', $deepSubCategory->id)->sortBy('name');
                            if ($productsInDeepSubCategory->isNotEmpty()) {
                                $deepNestedGroups->put($deepSubCategory->name, $productsInDeepSubCategory);
                            }
                        }
                        if ($deepNestedGroups->isNotEmpty()) {
                             $subCategoryGroups->put($subCategory->name, $deepNestedGroups);
                        }
                    } else {
                        $productsInSubCategory = $productsToGroup->where('category_id', $subCategory->id)->sortBy('name');
                        if ($productsInSubCategory->isNotEmpty()) {
                            $subCategoryGroups->put($subCategory->name, $productsInSubCategory);
                        }
                    }
                }
                if ($subCategoryGroups->isNotEmpty()) {
                    $grouped->put($mainCategory->name, $subCategoryGroups);
                }
            } else {
                $directProducts = $productsToGroup->where('category_id', $mainCategory->id)->sortBy('name');
                if ($directProducts->isNotEmpty()) {
                    $grouped->put($mainCategory->name, $directProducts);
                }
            }
        }
        return $grouped;
    }
}