<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoriesToInsert = [
            [
                'name' => 'ラーメン',
                'description' => '各種ラーメン',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'サイドメニュー',
                'description' => 'ラーメンと一緒にお楽しみいただけるメニュー',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ドリンク',
                'description' => '各種お飲み物',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'トッピング',
                'description' => 'ラーメンに追加できる具材',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // 既存のカテゴリがなければ挿入 (重複挿入防止の簡易策)
        foreach ($categoriesToInsert as $category) {
            if (!DB::table('categories')->where('name', $category['name'])->exists()) {
                DB::table('categories')->insert($category);
            }
        }

        // 親カテゴリのIDを取得
        $ramenCategoryId = DB::table('categories')->where('name', 'ラーメン')->first()->id;
        $sideMenuCategoryId = DB::table('categories')->where('name', 'サイドメニュー')->first()->id;
        $drinkCategoryId = DB::table('categories')->where('name', 'ドリンク')->first()->id;
        $toppingCategoryId = DB::table('categories')->where('name', 'トッピング')->first()->id;

        // 子カテゴリを投入 (重複挿入防止の簡易策)
        $subCategoriesToInsert = [
            // ラーメンの子カテゴリ（3種類を維持）
            [
                'name' => '醤油ラーメン',
                'description' => '伝統的な醤油ベースのラーメン',
                'parent_id' => $ramenCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '豚骨ラーメン',
                'description' => '濃厚な豚骨スープのラーメン',
                'parent_id' => $ramenCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '味噌ラーメン',
                'description' => '風味豊かな味噌ベースのラーメン',
                'parent_id' => $ramenCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 塩ラーメンも残しておきます。必要なければ削除してください。
            [
                'name' => '塩ラーメン',
                'description' => 'あっさりとした塩味のラーメン',
                'parent_id' => $ramenCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // サイドメニューの子カテゴリ
            [
                'name' => '唐揚げ',
                'description' => 'ジューシーな唐揚げ',
                'parent_id' => $sideMenuCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'チャーハン',
                'description' => '香ばしいチャーハン',
                'parent_id' => $sideMenuCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '餃子',
                'description' => '定番の焼き餃子',
                'parent_id' => $sideMenuCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 新規追加
            [
                'name' => 'ご飯物',
                'description' => 'ご飯系のサイドメニュー',
                'parent_id' => $sideMenuCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '一品料理',
                'description' => '単品で楽しめる料理',
                'parent_id' => $sideMenuCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ドリンクの子カテゴリ
            [
                'name' => 'ビール',
                'description' => '冷たいビール',
                'parent_id' => $drinkCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ソフトドリンク',
                'description' => '各種ソフトドリンク',
                'parent_id' => $drinkCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 新規追加
            [
                'name' => '日本酒',
                'description' => '日本各地の日本酒',
                'parent_id' => $drinkCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'その他アルコール',
                'description' => 'その他のアルコール飲料',
                'parent_id' => $drinkCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'チャーシュー',
                'description' => '追加の特製チャーシュー',
                'parent_id' => $toppingCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '味玉',
                'description' => '半熟の味付け煮卵',
                'parent_id' => $toppingCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ネギ',
                'description' => '新鮮な青ネギ',
                'parent_id' => $toppingCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'メンマ',
                'description' => 'コリコリとしたメンマ',
                'parent_id' => $toppingCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($subCategoriesToInsert as $subCategory) {
            if (!DB::table('categories')->where('name', $subCategory['name'])->exists()) {
                DB::table('categories')->insert($subCategory);
            }
        }
    }
}