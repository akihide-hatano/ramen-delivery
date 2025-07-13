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
// display_order順に最上位カテゴリ（親カテゴリ）を取得
        $mainCategories = Category::whereNull('parent_id')
                                    ->orderBy('display_order')
                                    ->get();

        $nestedGroupedProducts = collect(); // 最終的にビューに渡す多段階グループ化されたデータ

        foreach ($mainCategories as $mainCategory) {
            $mainCategoryProducts = collect(); // そのメインカテゴリ全体の商品（直下のもの）

            // そのメインカテゴリの子カテゴリを display_order 順に取得
            // with('products') で子カテゴリに紐付く商品もまとめてロード
            $subCategories = Category::where('parent_id', $mainCategory->id)
                                    ->orderBy('display_order')
                                    ->with(['products' => function($query) {
                                        $query->orderBy('name'); // 商品を名前順にソート
                                    }])
                                    ->get();

            // 子カテゴリがある場合
            if ($subCategories->isNotEmpty()) {
                $subCategoryGroups = collect();
                foreach ($subCategories as $subCategory) {
                    // さらに下位のカテゴリ（例: ビール、日本酒など）を取得
                    $nestedSubCategories = Category::where('parent_id', $subCategory->id)
                                                    ->orderBy('display_order')
                                                    ->with(['products' => function($query) {
                                                        $query->orderBy('name');
                                                    }])
                                                    ->get();

                    if ($nestedSubCategories->isNotEmpty()) {
                        // さらに深い階層のカテゴリがあれば、そのカテゴリ名でグループ化
                        $deepNestedGroups = collect();
                        foreach ($nestedSubCategories as $deepSubCategory) {
                            if ($deepSubCategory->products->isNotEmpty()) {
                                $deepNestedGroups->put($deepSubCategory->name, $deepSubCategory->products);
                            }
                        }
                        // 中間カテゴリ名の下に、さらに深いカテゴリのグループを追加
                        if ($deepNestedGroups->isNotEmpty()) {
                             $subCategoryGroups->put($subCategory->name, $deepNestedGroups);
                        }
                    } else {
                        // 最下層のカテゴリ（商品が直接紐付いているカテゴリ）の場合
                        if ($subCategory->products->isNotEmpty()) {
                            $subCategoryGroups->put($subCategory->name, $subCategory->products);
                        }
                    }
                }
                if ($subCategoryGroups->isNotEmpty()) {
                    $nestedGroupedProducts->put($mainCategory->name, $subCategoryGroups);
                }
            } else {
                // 最上位カテゴリに直接商品が紐付いている場合（例: 塩ラーメンがラーメンカテゴリに直接紐付いている場合）
                $directProducts = Product::where('category_id', $mainCategory->id)
                                        ->orderBy('name')
                                        ->get();
                if ($directProducts->isNotEmpty()) {
                    $nestedGroupedProducts->put($mainCategory->name, $directProducts);
                }
            }
        }

        // ここで $nestedGroupedProducts の構造は以下のようになります（例）：
        // [
        //   'ラーメン' => Collection (商品リスト),
        //   'サイドメニュー' => Collection (商品リスト),
        //   'ドリンク' => [ // コレクションの中にさらにコレクション
        //     'アルコール' => [
        //       'ビール' => Collection (生ビール),
        //       '日本酒' => Collection (地酒),
        //       'サワー・酎ハイ' => Collection (レモンサワー),
        //       // ...
        //     ],
        //     'ソフトドリンク' => [
        //       'お茶' => Collection (烏龍茶, 緑茶),
        //       '炭酸飲料' => Collection (コカ・コーラ, クラフトコーラ),
        //       'ジュース' => Collection (オレンジジュース),
        //       'その他ソフトドリンク' => Collection (自家製ジンジャーエール)
        //     ]
        //   ],
        //   'トッピング' => Collection (商品リスト),
        // ]
        // または、直接商品が紐付く場合は
        // 'ラーメン' => Collection (潮屋塩ラーメン, 味玉潮屋塩ラーメン, ...)

        return view('products.global_index', compact('nestedGroupedProducts'));
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