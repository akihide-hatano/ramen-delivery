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
        $ramenCategoryId = DB::table('categories')->where('name', 'ラーメン')->first()->id;
        $shoyuRamenId = DB::table('categories')->where('name', '醤油ラーメン')->first()->id;
        $tonkotsuRamenId = DB::table('categories')->where('name', '豚骨ラーメン')->first()->id;
        $misoRamenId = DB::table('categories')->where('name', '味噌ラーメン')->first()->id;

        $sideMenuCategoryId = DB::table('categories')->where('name', 'サイドメニュー')->first()->id;
        $karaageId = DB::table('categories')->where('name', '唐揚げ')->first()->id;
        $chahanId = DB::table('categories')->where('name', 'チャーハン')->first()->id;

        $drinkCategoryId = DB::table('categories')->where('name', 'ドリンク')->first()->id;
        $beerId = DB::table('categories')->where('name', 'ビール')->first()->id;

        $toppingCategoryId = DB::table('categories')->where('name', 'トッピング')->first()->id;


        // 必要な店舗IDを取得
        $shop1Id = DB::table('shops')->where('name', '博多ラーメン 豚骨亭')->first()->id;
        $shop2Id = DB::table('shops')->where('name', '札幌味噌ラーメン 麺匠')->first()->id;
        $shop3Id = DB::table('shops')->where('name', '中華そば 懐かし屋')->first()->id;

        DB::table('products')->insert([
            // --- 博多ラーメン 豚骨亭 の商品 ---
            [
                'shop_id' => $shop1Id,
                'category_id' => $tonkotsuRamenId, // 豚骨ラーメンカテゴリ
                'name' => '特製豚骨ラーメン',
                'description' => '定番の濃厚豚骨スープに特製チャーシューを添えました。',
                'price' => 950,
                'image_url' => 'https://example.com/tonkotsu_ramen_special.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_id' => $shop1Id,
                'category_id' => $tonkotsuRamenId, // 豚骨ラーメンカテゴリ
                'name' => '辛味噌豚骨ラーメン',
                'description' => 'ピリ辛の特製味噌を加えた豚骨ラーメン。',
                'price' => 1000,
                'image_url' => 'https://example.com/tonkotsu_ramen_spicy.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_id' => $shop1Id,
                'category_id' => $toppingCategoryId, // トッピングカテゴリ
                'name' => '替え玉',
                'description' => '追加の麺。',
                'price' => 150,
                'image_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_id' => $shop1Id,
                'category_id' => $karaageId, // 唐揚げカテゴリ
                'name' => '一口餃子（6個）',
                'description' => '博多名物の一口餃子。',
                'price' => 350,
                'image_url' => 'https://example.com/hitokuchi_gyoza.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // --- 札幌味噌ラーメン 麺匠 の商品 ---
            [
                'shop_id' => $shop2Id,
                'category_id' => $misoRamenId, // 味噌ラーメンカテゴリ
                'name' => '濃厚味噌ラーメン',
                'description' => '秘伝の味噌ダレと太麺が絡み合う、当店一番人気。',
                'price' => 950,
                'image_url' => 'https://example.com/miso_ramen_rich.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_id' => $shop2Id,
                'category_id' => $misoRamenId, // 味噌ラーメンカテゴリ
                'name' => '辛味噌ラーメン',
                'description' => '特製辛味噌が食欲をそそる。',
                'price' => 1000,
                'image_url' => 'https://example.com/miso_ramen_spicy.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_id' => $shop2Id,
                'category_id' => $chahanId, // チャーハンカテゴリ
                'name' => '半チャーハン',
                'description' => 'ラーメンとの相性抜群の半チャーハン。',
                'price' => 400,
                'image_url' => 'https://example.com/half_fried_rice.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_id' => $shop2Id,
                'category_id' => $karaageId, // 唐揚げカテゴリ
                'name' => '若鶏の唐揚げ（3個）',
                'description' => 'ジューシーな若鶏の唐揚げ。',
                'price' => 300,
                'image_url' => 'https://example.com/karaage.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // --- 中華そば 懐かし屋 の商品 ---
            [
                'shop_id' => $shop3Id,
                'category_id' => $shoyuRamenId, // 醤油ラーメンカテゴリ
                'name' => '中華そば（並）',
                'description' => 'あっさりとした昔ながらの醤油味。',
                'price' => 750,
                'image_url' => 'https://example.com/shoyu_ramen_regular.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_id' => $shop3Id,
                'category_id' => $shoyuRamenId, // 醤油ラーメンカテゴリ
                'name' => '中華そば（大）',
                'description' => '麺大盛りの醤油ラーメン。',
                'price' => 850,
                'image_url' => 'https://example.com/shoyu_ramen_large.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_id' => $shop3Id,
                'category_id' => $beerId, // ビールカテゴリ
                'name' => '瓶ビール（中瓶）',
                'description' => '定番の国産瓶ビール。',
                'price' => 550,
                'image_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_id' => $shop3Id,
                'category_id' => $drinkCategoryId, // ドリンクカテゴリ（ビールではない一般的なドリンク）
                'name' => 'ウーロン茶',
                'description' => 'さっぱりと飲みやすい烏龍茶。',
                'price' => 200,
                'image_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}