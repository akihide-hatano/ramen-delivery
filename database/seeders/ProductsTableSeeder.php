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


        // 既存の店舗ID取得
        $shop1Id = DB::table('shops')->where('name', '博多ラーメン 豚骨亭')->first()->id;
        $shop2Id = DB::table('shops')->where('name', '札幌味噌ラーメン 麺匠')->first()->id;
        $shop3Id = DB::table('shops')->where('name', '中華そば 懐かし屋')->first()->id;
        $ushioyaNambaId = DB::table('shops')->where('name', 'ラーメン潮屋 大阪難波店')->first()->id;
        $ushioyaUmedaId = DB::table('shops')->where('name', 'ラーメン潮屋 梅田店')->first()->id;
        $ushioyaShibataId = DB::table('shops')->where('name', 'ラーメン潮屋 芝田店')->first()->id;

        // --- ここから新しい店舗IDを取得 ---
        $ushioyaKawaSanjoId = DB::table('shops')->where('name', 'ラーメン潮屋 河原町三条店')->first()->id;
        $ushioyaKawaShijoId = DB::table('shops')->where('name', 'ラーメン潮屋 河原町四条店')->first()->id;
        $ushioyaOmiyaId = DB::table('shops')->where('name', 'ラーメン潮屋 大宮店')->first()->id;
        $ushioyaKyotoStId = DB::table('shops')->where('name', 'ラーメン潮屋 京都駅店')->first()->id;
        $ushioyaKarasumaId = DB::table('shops')->where('name', 'ラーメン潮屋 烏丸店')->first()->id;
        $ushioyaKarasuma7Id = DB::table('shops')->where('name', 'ラーメン潮屋 烏丸七条店')->first()->id;


        DB::table('products')->insert([
            // --- (既存の商品のデータは省略。上記ファイルの末尾に追加してください) ---

            // --- ラーメン潮屋 河原町三条店 の商品 ---
            [
                'shop_id' => $ushioyaKawaSanjoId,
                'category_id' => $shoyuRamenId,
                'name' => '潮屋特製ラーメン',
                'description' => '魚介の旨味が凝縮された、あっさり醤油スープが自慢。',
                'price' => 880,
                'image_url' => 'https://example.com/ushioya_kawasanjyo_ramen.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_id' => $ushioyaKawaSanjoId,
                'category_id' => $toppingCategoryId,
                'name' => '全部のせトッピング',
                'description' => 'チャーシュー、味玉、メンマ、ネギ増量！',
                'price' => 300,
                'image_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // --- ラーメン潮屋 河原町四条店 の商品 ---
            [
                'shop_id' => $ushioyaKawaShijoId,
                'category_id' => $shoyuRamenId,
                'name' => '潮屋ラーメン（定番）',
                'description' => '迷ったらこれ！潮屋の基本の一杯。',
                'price' => 850,
                'image_url' => 'https://example.com/ushioya_kawashijo_ramen.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_id' => $ushioyaKawaShijoId,
                'category_id' => $chahanId,
                'name' => 'ミニ焼き飯',
                'description' => 'ラーメンのお供に最適なミニサイズの焼き飯。',
                'price' => 400,
                'image_url' => 'https://example.com/ushioya_yakimeshi.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // --- ラーメン潮屋 大宮店 の商品 ---
            [
                'shop_id' => $ushioyaOmiyaId,
                'category_id' => $shoyuRamenId,
                'name' => '潮屋ラーメン（あっさり）',
                'description' => '女性にも人気の、さらにあっさりとした味わい。',
                'price' => 820,
                'image_url' => 'https://example.com/ushioya_omiya_ramen.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_id' => $ushioyaOmiyaId,
                'category_id' => $karaageId,
                'name' => '鶏の唐揚げ（3個）',
                'description' => '外はカリッと中はジューシーな唐揚げ。',
                'price' => 350,
                'image_url' => 'https://example.com/karaage.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // --- ラーメン潮屋 京都駅店 の商品 ---
            [
                'shop_id' => $ushioyaKyotoStId,
                'category_id' => $shoyuRamenId,
                'name' => '京都駅限定ラーメン',
                'description' => '駅店限定の特別な一杯。',
                'price' => 900,
                'image_url' => 'https://example.com/ushioya_kyoto_st_ramen.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_id' => $ushioyaKyotoStId,
                'category_id' => $drinkCategoryId,
                'name' => '緑茶',
                'description' => '食後にさっぱりと。',
                'price' => 180,
                'image_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // --- ラーメン潮屋 烏丸店 の商品 ---
            [
                'shop_id' => $ushioyaKarasumaId,
                'category_id' => $shoyuRamenId,
                'name' => '烏丸スペシャルラーメン',
                'description' => 'オフィス街で人気の烏丸店限定メニュー。',
                'price' => 920,
                'image_url' => 'https://example.com/ushioya_karasuma_ramen.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_id' => $ushioyaKarasumaId,
                'category_id' => $chahanId,
                'name' => '高菜ご飯',
                'description' => 'ピリ辛高菜が食欲をそそるご飯もの。',
                'price' => 250,
                'image_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // --- ラーメン潮屋 烏丸七条店 の商品 ---
            [
                'shop_id' => $ushioyaKarasuma7Id,
                'category_id' => $shoyuRamenId,
                'name' => '潮屋ラーメンセット',
                'description' => 'ラーメンと選べるサイドメニューのお得なセット。',
                'price' => 1100,
                'image_url' => 'https://example.com/ushioya_karasuma7_set.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_id' => $ushioyaKarasuma7Id,
                'category_id' => $beerId,
                'name' => 'ハイボール',
                'description' => 'ラーメンと相性抜群！',
                'price' => 400,
                'image_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}