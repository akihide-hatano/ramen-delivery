<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('products')->truncate(); // 開発時に全商品をクリアする場合

        // 必要なカテゴリIDを取得（より詳細な子カテゴリまで取得）
        // これらのカテゴリが存在することをCategoriesTableSeederで確認してください
        $shoyuRamenId = DB::table('categories')->where('name', '醤油ラーメン')->first()->id;
        $tonkotsuRamenId = DB::table('categories')->where('name', '豚骨ラーメン')->first()->id;
        $misoRamenId = DB::table('categories')->where('name', '味噌ラーメン')->first()->id;
        $shioRamenId = DB::table('categories')->where('name', '塩ラーメン')->first()->id; // 追加

        $karaageId = DB::table('categories')->where('name', '唐揚げ')->first()->id;
        $chahanId = DB::table('categories')->where('name', 'チャーハン')->first()->id;
        $gohanmonoId = DB::table('categories')->where('name', 'ご飯物')->first()->id;
        $ippinryoriId = DB::table('categories')->where('name', '一品料理')->first()->id;

        // ドリンク関連のカテゴリを細かく取得
        $beerId = DB::table('categories')->where('name', 'ビール')->first()->id;
        $nihonshuId = DB::table('categories')->where('name', '日本酒')->first()->id;
        $shochuId = DB::table('categories')->where('name', '焼酎')->first()->id;
        $sourChuhaiId = DB::table('categories')->where('name', 'サワー・酎ハイ')->first()->id;
        $otherAlcoholId = DB::table('categories')->where('name', 'その他アルコール')->first()->id;

        $ochaId = DB::table('categories')->where('name', 'お茶')->first()->id;
        $tansanInryoId = DB::table('categories')->where('name', '炭酸飲料')->first()->id;
        $juiceId = DB::table('categories')->where('name', 'ジュース')->first()->id;
        $otherSoftDrinkId = DB::table('categories')->where('name', 'その他ソフトドリンク')->first()->id;

        // トッピング関連のカテゴリを取得
        $toppingCategoryId = DB::table('categories')->where('name', 'トッピング')->first()->id; // これを追加！
        $chashuId = DB::table('categories')->where('name', 'チャーシュー')->first()->id;
        $ajitamagoId = DB::table('categories')->where('name', '味玉')->first()->id;
        $negiId = DB::table('categories')->where('name', 'ネギ')->first()->id;
        $menmaId = DB::table('categories')->where('name', 'メンマ')->first()->id;


        // 既存の店舗ID取得 (全て取得)
        $shops = DB::table('shops')->get();

        // ラーメン潮屋の店舗IDのみを抽出
        $ushioyaShopIds = $shops->filter(function ($shop) {
            return str_contains($shop->name, 'ラーメン潮屋');
        })->pluck('id')->toArray();

        // --- 共通商品ラインナップを定義 ---
        // 各店舗に共通で提供される商品のリスト
        $commonUshioyaProducts = [
            // ラーメン
            [
                'category_id' => $shoyuRamenId,
                'name' => '潮屋ラーメン',
                'description' => '魚介系のあっさり醤油スープが人気の看板メニュー。',
                'price' => 880,
                'image_url' => 'https://example.com/ushioya_standard_ramen.jpg',
            ],
            [
                'category_id' => $shoyuRamenId,
                'name' => '味玉潮屋ラーメン',
                'description' => '潮屋ラーメンに特製味玉をトッピング。',
                'price' => 980,
                'image_url' => 'https://example.com/ushioya_ajitama_ramen.jpg',
            ],
            [
                'category_id' => $tonkotsuRamenId,
                'name' => 'とんこつラーメン',
                'description' => '濃厚な豚骨スープが特徴のラーメン。',
                'price' => 900,
                'image_url' => 'https://example.com/ushioya_tonkotsu_ramen.jpg',
            ],
            [
                'category_id' => $misoRamenId,
                'name' => '味噌ラーメン',
                'description' => '風味豊かな味噌スープが食欲をそそる。',
                'price' => 920,
                'image_url' => 'https://example.com/ushioya_miso_ramen.jpg',
            ],
            // サイドメニュー
            [
                'category_id' => $chahanId,
                'name' => '半チャーハン',
                'description' => 'ラーメンと相性抜群のミニチャーハン。',
                'price' => 400,
                'image_url' => 'https://example.com/ushioya_half_chahan.jpg',
            ],
            [
                'category_id' => $gohanmonoId,
                'name' => 'ライス',
                'description' => 'ご飯単品。大盛りもできます。',
                'price' => 150,
                'image_url' => null,
            ],
            [
                'category_id' => $gohanmonoId, // ご飯物
                'name' => 'ミニチャーシュー丼',
                'description' => '特製チャーシューが乗ったご飯。',
                'price' => 380,
                'image_url' => 'https://example.com/ushioya_mini_chashu_don.jpg',
            ],
            [
                'category_id' => $karaageId,
                'name' => '鶏の唐揚げ（3個）',
                'description' => '外はカリッと中はジューシー。',
                'price' => 350,
                'image_url' => 'https://example.com/ushioya_karaage.jpg',
            ],
            [
                'category_id' => $ippinryoriId,
                'name' => '特製餃子（5個）',
                'description' => '肉汁あふれる特製餃子。',
                'price' => 450,
                'image_url' => 'https://example.com/ushioya_gyoza.jpg',
            ],
            [
                'category_id' => $ippinryoriId, // 一品料理
                'name' => 'ピリ辛メンマ',
                'description' => '箸休めに最適なピリ辛メンマ。',
                'price' => 280,
                'image_url' => null,
            ],

            // ドリンク
            [
                'category_id' => $beerId,
                'name' => '生ビール（中ジョッキ）',
                'description' => '冷たい生ビール。',
                'price' => 500,
                'image_url' => null,
            ],
            [
                'category_id' => $ochaId,
                'name' => '烏龍茶',
                'description' => 'さっぱりとしたウーロン茶。',
                'price' => 200,
                'image_url' => null,
            ],
            [
                'category_id' => $tansanInryoId,
                'name' => 'コカ・コーラ',
                'description' => '定番のコーラ。',
                'price' => 220,
                'image_url' => null,
            ],
            [
                'category_id' => $juiceId,
                'name' => 'オレンジジュース',
                'description' => '100%オレンジジュース。',
                'price' => 220,
                'image_url' => null,
            ],
            [
                'category_id' => $nihonshuId,
                'name' => '地酒（冷）',
                'description' => '季節限定の地酒。',
                'price' => 600,
                'image_url' => null,
            ],
            [
                'category_id' => $sourChuhaiId, // サワー・酎ハイ
                'name' => 'レモンサワー',
                'description' => '爽やかなレモンサワー。',
                'price' => 400,
                'image_url' => null,
            ],
            [
                'category_id' => $ochaId, // お茶
                'name' => '緑茶',
                'description' => '食事に合う緑茶。',
                'price' => 200,
                'image_url' => null,
            ],

            // トッピング
            [
                'category_id' => $toppingCategoryId, // トッピング親カテゴリ
                'name' => '替え玉',
                'description' => '追加の麺。',
                'price' => 150,
                'image_url' => null,
            ],
            [
                'category_id' => $ajitamagoId,
                'name' => '特製味玉',
                'description' => 'とろーり半熟の味付け卵。',
                'price' => 120,
                'image_url' => null,
            ],
            [
                'category_id' => $chashuId,
                'name' => '追加チャーシュー（3枚）',
                'description' => 'とろとろの自家製チャーシュー。',
                'price' => 250,
                'image_url' => null,
            ],
            [
                'category_id' => $negiId,
                'name' => '九条ネギ増し',
                'description' => '香りの良い九条ネギをたっぷり。',
                'price' => 100,
                'image_url' => null,
            ],
            [
                'category_id' => $menmaId,
                'name' => 'メンマ増し',
                'description' => 'コリコリ食感のメンマを増量。',
                'price' => 100,
                'image_url' => null,
            ],
        ];

        $productsToInsert = [];

        // ラーメン潮屋の各店舗に共通商品を割り当てる
        foreach ($ushioyaShopIds as $shopId) {
            foreach ($commonUshioyaProducts as $productData) {
                // 同じshop_idとnameの組み合わせで重複がないかチェック
                $exists = DB::table('products')
                            ->where('shop_id', $shopId)
                            ->where('name', $productData['name'])
                            ->exists();
                if (!$exists) {
                    $productsToInsert[] = array_merge($productData, [
                        'shop_id' => $shopId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            // 各店舗に追加する特別な2商品（共通商品とは別のものを想定）
            // この部分は、店舗ごとに異なる商品を割り当てるためのロジックです
            $shopSpecificProducts = [
                [
                    'category_id' => $shoyuRamenId,
                    'name' => '特製つけ麺',
                    'description' => '特製の魚介豚骨スープにつけて食べる麺。',
                    'price' => 950,
                    'image_url' => 'https://example.com/ushioya_tsukemen.jpg',
                ],
                [
                    'category_id' => $shioRamenId, // 塩ラーメンカテゴリに紐付け
                    'name' => 'あっさり塩ラーメン',
                    'description' => '鶏ガラベースのあっさり塩味ラーメン。',
                    'price' => 850,
                    'image_url' => 'https://example.com/ushioya_shio_ramen.jpg',
                ],
            ];

            foreach ($shopSpecificProducts as $specificProductData) {
                $exists = DB::table('products')
                            ->where('shop_id', $shopId)
                            ->where('name', $specificProductData['name'])
                            ->exists();
                if (!$exists) {
                    $productsToInsert[] = array_merge($specificProductData, [
                        'shop_id' => $shopId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // --- 個別の店舗の商品データ（もしあれば、共通商品や店舗ごとの共通追加とは別に） ---
        // こちらも重複挿入防止のチェックを追加
        $ushioyaKyotoStId = DB::table('shops')->where('name', 'ラーメン潮屋 京都駅店')->first()->id;
        $ushioyaKawaSanjoId = DB::table('shops')->where('name', 'ラーメン潮屋 河原町三条店')->first()->id;

        $uniqueShopProducts = [
            [
                'shop_id' => $ushioyaKyotoStId,
                'category_id' => $shoyuRamenId,
                'name' => '京都駅限定ラーメン',
                'description' => '駅店限定の特別な一杯。',
                'price' => 900,
                'image_url' => 'https://example.com/ushioya_kyoto_st_ramen.jpg',
            ],
            [
                'shop_id' => $ushioyaKawaSanjoId,
                'category_id' => $shoyuRamenId,
                'name' => '潮屋特製ラーメン（河原町三条限定）', // 名前をより明確に
                'description' => '魚介の旨味が凝縮された、あっさり醤油スープが自慢。（河原町三条限定の少し特別な配合を想定）',
                'price' => 880,
                'image_url' => 'https://example.com/ushioya_kawasanjyo_ramen.jpg',
            ],
        ];

        foreach ($uniqueShopProducts as $productData) {
            $exists = DB::table('products')
                        ->where('shop_id', $productData['shop_id'])
                        ->where('name', $productData['name'])
                        ->exists();
            if (!$exists) {
                $productsToInsert[] = array_merge($productData, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 全ての準備ができた商品を一括挿入
        if (!empty($productsToInsert)) {
            DB::table('products')->insert($productsToInsert);
        }
    }
}