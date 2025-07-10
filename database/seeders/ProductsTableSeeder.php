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
        // 必要なカテゴリIDを取得（子カテゴリも含む）
        $shoyuRamenId = DB::table('categories')->where('name', '醤油ラーメン')->first()->id;
        $tonkotsuRamenId = DB::table('categories')->where('name', '豚骨ラーメン')->first()->id;
        $misoRamenId = DB::table('categories')->where('name', '味噌ラーメン')->first()->id;
        $karaageId = DB::table('categories')->where('name', '唐揚げ')->first()->id;
        $chahanId = DB::table('categories')->where('name', 'チャーハン')->first()->id;
        $drinkCategoryId = DB::table('categories')->where('name', 'ドリンク')->first()->id;
        $beerId = DB::table('categories')->where('name', 'ビール')->first()->id;
        $toppingCategoryId = DB::table('categories')->where('name', 'トッピング')->first()->id;

        // 既存の店舗ID取得 (全て取得)
        $shops = DB::table('shops')->get();

        // ラーメン潮屋の店舗IDのみを抽出
        $ushioyaShopIds = $shops->filter(function ($shop) {
            return str_contains($shop->name, 'ラーメン潮屋');
        })->pluck('id')->toArray();

        // --- 共通商品ラインナップを定義 ---
        $commonUshioyaProducts = [
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
                'category_id' => $chahanId,
                'name' => '半チャーハン',
                'description' => 'ラーメンと相性抜群のミニチャーハン。',
                'price' => 400,
                'image_url' => 'https://example.com/ushioya_half_chahan.jpg',
            ],
            [
                'category_id' => $karaageId,
                'name' => '鶏の唐揚げ（3個）',
                'description' => '外はカリッと中はジューシー。',
                'price' => 350,
                'image_url' => 'https://example.com/ushioya_karaage.jpg',
            ],
            [
                'category_id' => $beerId,
                'name' => '生ビール',
                'description' => '冷たい生ビール。',
                'price' => 500,
                'image_url' => null,
            ],
            [
                'category_id' => $drinkCategoryId,
                'name' => 'ウーロン茶',
                'description' => 'さっぱりとしたウーロン茶。',
                'price' => 200,
                'image_url' => null,
            ],
            [
                'category_id' => $toppingCategoryId,
                'name' => '替え玉',
                'description' => '追加の麺。',
                'price' => 150,
                'image_url' => null,
            ],
            [
                'category_id' => $toppingCategoryId,
                'name' => '特製味玉',
                'description' => 'とろーり半熟の味付け卵。',
                'price' => 120,
                'image_url' => null,
            ],
        ];

        $productsToInsert = [];

        // ラーメン潮屋の各店舗に共通商品を割り当てる
        foreach ($ushioyaShopIds as $shopId) {
            foreach ($commonUshioyaProducts as $productData) {
                $productsToInsert[] = array_merge($productData, [
                    'shop_id' => $shopId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // --- 個別の店舗の商品データ（もしあれば） ---
        // 例: 京都駅店限定ラーメンなど、特定の店舗にしかない商品
        $ushioyaKyotoStId = DB::table('shops')->where('name', 'ラーメン潮屋 京都駅店')->first()->id; // 必要ならIDを再取得
        $ushioyaKawaSanjoId = DB::table('shops')->where('name', 'ラーメン潮屋 河原町三条店')->first()->id; // 必要ならIDを再取得

        $productsToInsert[] = [
            'shop_id' => $ushioyaKyotoStId,
            'category_id' => $shoyuRamenId,
            'name' => '京都駅限定ラーメン',
            'description' => '駅店限定の特別な一杯。',
            'price' => 900,
            'image_url' => 'https://example.com/ushioya_kyoto_st_ramen.jpg',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $productsToInsert[] = [
            'shop_id' => $ushioyaKawaSanjoId,
            'category_id' => $shoyuRamenId,
            'name' => '潮屋特製ラーメン', // 既存データ名と重複しているが、シーダーとしては問題なし
            'description' => '魚介の旨味が凝縮された、あっさり醤油スープが自慢。（河原町三条限定の少し特別な配合を想定）',
            'price' => 880,
            'image_url' => 'https://example.com/ushioya_kawasanjyo_ramen.jpg',
            'created_at' => now(),
            'updated_at' => now(),
        ];


        DB::table('products')->insert($productsToInsert);
    }
}