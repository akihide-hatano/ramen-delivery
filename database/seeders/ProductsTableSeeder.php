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
        // 必要なカテゴリIDを取得（CategoriesTableSeederで作成されたカテゴリが存在することを確認してください）
        $shoyuRamenId = DB::table('categories')->where('name', '醤油ラーメン')->first()->id;
        $tonkotsuRamenId = DB::table('categories')->where('name', '豚骨ラーメン')->first()->id;
        $misoRamenId = DB::table('categories')->where('name', '味噌ラーメン')->first()->id;
        $shioRamenId = DB::table('categories')->where('name', '塩ラーメン')->first()->id;

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
        $toppingCategoryId = DB::table('categories')->where('name', 'トッピング')->first()->id; // 親カテゴリ
        $chashuId = DB::table('categories')->where('name', 'チャーシュー')->first()->id;
        $ajitamagoId = DB::table('categories')->where('name', '味玉')->first()->id;
        $negiId = DB::table('categories')->where('name', 'ネギ')->first()->id;
        $menmaId = DB::table('categories')->where('name', 'メンマ')->first()->id;


        // 既存の店舗ID取得 (全て取得)
        $allShops = DB::table('shops')->get(); // 全ての店舗を取得

        // ラーメン潮屋の店舗IDのみを抽出 (共通商品を割り当てるため)
        $ushioyaShopIds = $allShops->filter(function ($shop) {
            return str_contains($shop->name, 'ラーメン潮屋');
        })->pluck('id')->toArray();

        // --- 共通商品ラインナップを定義 ---
        // ラーメンは共通で「潮屋塩ラーメン」のみとし、味変は各店舗の個別商品で提供
        $commonUshioyaProducts = [
            // 基本ラーメン
            [
                'category_id' => $shioRamenId, // 塩ラーメンをベースに
                'name' => '潮屋塩ラーメン',
                'description' => '魚介系のあっさりとした特製塩スープが自慢の基本メニュー。',
                'price' => 880,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Shioya+Base+Ramen',
            ],
            [
                'category_id' => $shioRamenId,
                'name' => '味玉潮屋塩ラーメン',
                'description' => '潮屋塩ラーメンに特製味玉をトッピング。',
                'price' => 980,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Ajitama+Shio+Ramen',
            ],
            // サイドメニュー (ここは前回と同じ)
            [
                'category_id' => $chahanId,
                'name' => '半チャーハン',
                'description' => 'ラーメンと相性抜群のミニチャーハン。',
                'price' => 400,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Half+Chahan',
            ],
            [
                'category_id' => $gohanmonoId,
                'name' => 'ライス',
                'description' => 'ご飯単品。大盛りもできます。',
                'price' => 150,
                'image_url' => null,
            ],
            [
                'category_id' => $gohanmonoId,
                'name' => 'ミニチャーシュー丼',
                'description' => '特製チャーシューが乗ったご飯。',
                'price' => 380,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Mini+Chashu+Don',
            ],
            [
                'category_id' => $karaageId,
                'name' => '鶏の唐揚げ（3個）',
                'description' => '外はカリッと中はジューシー。',
                'price' => 350,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Karaage',
            ],
            [
                'category_id' => $ippinryoriId,
                'name' => '特製餃子（5個）',
                'description' => '肉汁あふれる特製餃子。',
                'price' => 450,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Gyoza',
            ],
            [
                'category_id' => $ippinryoriId,
                'name' => 'ピリ辛メンマ',
                'description' => '箸休めに最適なピリ辛メンマ。',
                'price' => 280,
                'image_url' => null,
            ],

            // ドリンク (前回と同じ)
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
                'category_id' => $sourChuhaiId,
                'name' => 'レモンサワー',
                'description' => '爽やかなレモンサワー。',
                'price' => 400,
                'image_url' => null,
            ],
            [
                'category_id' => $ochaId,
                'name' => '緑茶',
                'description' => '食事に合う緑茶。',
                'price' => 200,
                'image_url' => null,
            ],

            // トッピング (前回と同じ)
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
        }

        // --- 各店舗に個別の「塩ラーメン味変」商品を追加 ---
        foreach ($allShops as $shop) {
            $shopId = $shop->id;
            $shopName = $shop->name;

            $specificProductsForThisShop = [];

            // 店舗名に基づいてユニークな塩ラーメン味変メニューを設定
            if (str_contains($shopName, '大阪難波店')) {
                $specificProductsForThisShop = [
                    [
                        'category_id' => $shioRamenId,
                        'name' => '難波限定！焦がし醤油塩ラーメン', // 塩ラーメンベースの味変
                        'description' => '焦がし醤油の香ばしさが際立つ、難波店限定の塩ラーメン。',
                        'price' => 950,
                        'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Namba+Kogashi+Shio',
                    ],
                    [
                        'category_id' => $shioRamenId,
                        'name' => 'バターコーン塩ラーメン',
                        'description' => '北海道産バターと甘いコーンでまろやかに仕上げた塩ラーメン。',
                        'price' => 1000,
                        'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Butter+Corn+Shio',
                    ],
                ];
            } elseif (str_contains($shopName, '梅田店')) {
                $specificProductsForThisShop = [
                    [
                        'category_id' => $shioRamenId,
                        'name' => '梅田特製！梅しそ塩ラーメン',
                        'description' => '紀州梅と大葉でさっぱりと仕上げた、梅田店限定の塩ラーメン。',
                        'price' => 960,
                        'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Umeda+Ume+Shiso+Shio',
                    ],
                    [
                        'category_id' => $shioRamenId,
                        'name' => '背脂こってり塩ラーメン',
                        'description' => '塩ベースに背脂を加え、こってり感を増した一杯。',
                        'price' => 990,
                        'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Seabura+Shio',
                    ],
                ];
            } elseif (str_contains($shopName, '芝田店')) {
                $specificProductsForThisShop = [
                    [
                        'category_id' => $shioRamenId,
                        'name' => '芝田限定！レモン塩ラーメン',
                        'description' => 'フレッシュレモンを絞っていただく、爽やかな塩ラーメン。',
                        'price' => 950,
                        'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Shibata+Lemon+Shio',
                    ],
                    [
                        'category_id' => $shioRamenId,
                        'name' => 'ピリ辛坦々塩ラーメン',
                        'description' => '塩スープにラー油と肉味噌でピリ辛に仕上げた坦々風塩ラーメン。',
                        'price' => 1020,
                        'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Tantan+Shio',
                    ],
                ];
            } elseif (str_contains($shopName, '河原町三条店')) {
                $specificProductsForThisShop = [
                    [
                        'category_id' => $shioRamenId,
                        'name' => '河原町三条限定！柚子塩ラーメン',
                        'description' => '柚子の香りが広がる、京都らしい上品な塩ラーメン。',
                        'price' => 970,
                        'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=KawaSanjo+Yuzu+Shio',
                    ],
                    [
                        'category_id' => $shioRamenId,
                        'name' => '鶏白湯塩ラーメン',
                        'description' => '濃厚な鶏白湯スープを塩ベースで仕上げたコク深い一杯。',
                        'price' => 1050,
                        'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Toripaitan+Shio',
                    ],
                ];
            } elseif (str_contains($shopName, '河原町四条店')) {
                $specificProductsForThisShop = [
                    [
                        'category_id' => $shioRamenId,
                        'name' => '四条限定！焦がしネギ塩ラーメン',
                        'description' => '香ばしい焦がしネギの風味が食欲をそそる塩ラーメン。',
                        'price' => 980,
                        'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Shijo+Kogashi+Negi+Shio',
                    ],
                    [
                        'category_id' => $shioRamenId,
                        'name' => '京都九条ネギ塩ラーメン',
                        'description' => '京都九条ネギをふんだんに使った、風味豊かな塩ラーメン。',
                        'price' => 1000,
                        'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Kujo+Negi+Shio',
                    ],
                ];
            } elseif (str_contains($shopName, '大宮店')) {
                $specificProductsForThisShop = [
                    [
                        'category_id' => $shioRamenId,
                        'name' => '大宮限定！焦がしにんにく塩ラーメン',
                        'description' => 'ガツンと効いた焦がしにんにくが特徴の塩ラーメン。',
                        'price' => 990,
                        'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Omiya+Garlic+Shio',
                    ],
                    [
                        'category_id' => $shioRamenId,
                        'name' => '背脂味噌塩ラーメン', // 塩ベースに味噌と背脂でパンチを
                        'description' => '塩スープに背脂と少量の味噌を加え、深みを出した限定ラーメン。',
                        'price' => 1030,
                        'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Omiya+Miso+Shio',
                    ],
                ];
            } elseif (str_contains($shopName, '京都駅店')) {
                $specificProductsForThisShop = [
                    [
                        'category_id' => $shioRamenId,
                        'name' => '京都駅限定！京鴨塩ラーメン',
                        'description' => '京鴨の出汁が効いた、京都駅店限定の贅沢な塩ラーメン。',
                        'price' => 1100,
                        'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Kyoto+Duck+Shio',
                    ],
                    [
                        'category_id' => $shioRamenId,
                        'name' => 'とろろ昆布塩ラーメン',
                        'description' => '磯の香りととろみが塩ラーメンと絶妙に絡む一杯。',
                        'price' => 960,
                        'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Tororo+Kombu+Shio',
                    ],
                ];
            } elseif (str_contains($shopName, '烏丸店')) {
                $specificProductsForThisShop = [
                    [
                        'category_id' => $shioRamenId,
                        'name' => '烏丸限定！海老塩ラーメン',
                        'description' => '海老の旨味が凝縮された、香ばしい塩ラーメン。',
                        'price' => 1020,
                        'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Karasuma+Shrimp+Shio',
                    ],
                    [
                        'category_id' => $shioRamenId,
                        'name' => '塩豚骨ラーメン', // 塩ベースの豚骨
                        'description' => '濃厚豚骨スープを塩味でさっぱりと仕上げた一杯。',
                        'price' => 980,
                        'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Shio+Tonkotsu',
                    ],
                ];
            } elseif (str_contains($shopName, '烏丸七条店')) {
                $specificProductsForThisShop = [
                    [
                        'category_id' => $shioRamenId,
                        'name' => '七条限定！アサリ塩ラーメン',
                        'description' => 'アサリの出汁が効いた、魚介の旨味あふれる塩ラーメン。',
                        'price' => 1000,
                        'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Shichijo+Clam+Shio',
                    ],
                    [
                        'category_id' => $shioRamenId,
                        'name' => '辛味噌塩ラーメン', // 塩ベースの辛味噌
                        'description' => '塩スープに自家製辛味噌を溶かした、ピリ辛の一杯。',
                        'price' => 990,
                        'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Spicy+Miso+Shio',
                    ],
                ];
            }

            foreach ($specificProductsForThisShop as $specificProductData) {
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

        // 全ての準備ができた商品を一括挿入
        if (!empty($productsToInsert)) {
            DB::table('products')->insert($productsToInsert);
        }
    }
}