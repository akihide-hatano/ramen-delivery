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
        // 既存データをクリアしてから新しいデータを投入する場合（開発環境向け）
        // DB::table('categories')->truncate(); // 注意: これを実行すると既存データが全て消えます

        // まず、最上位の親カテゴリを挿入
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

        foreach ($categoriesToInsert as $category) {
            if (!DB::table('categories')->where('name', $category['name'])->exists()) {
                DB::table('categories')->insert($category);
            }
        }

        // 親カテゴリのIDを取得
        $ramenCategoryId = DB::table('categories')->where('name', 'ラーメン')->first()->id;
        $sideMenuCategoryId = DB::table('categories')->where('name', 'サイドメニュー')->first()->id;
        $drinkCategoryId = DB::table('categories')->where('name', 'ドリンク')->first()->id; // ドリンクの親カテゴリID
        $toppingCategoryId = DB::table('categories')->where('name', 'トッピング')->first()->id;

        // 子カテゴリを投入 (重複挿入防止の簡易策)
        $subCategoriesToInsert = [
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
            [
                'name' => '塩ラーメン',
                'description' => 'あっさりとした塩味のラーメン',
                'parent_id' => $ramenCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],

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

            [
                'name' => 'アルコール',
                'description' => 'アルコール飲料',
                'parent_id' => $drinkCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ソフトドリンク',
                'description' => '清涼飲料水など',
                'parent_id' => $drinkCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($subCategoriesToInsert as $subCategory) {
            if (!DB::table('categories')->where('name', $subCategory['name'])->exists()) {
                DB::table('categories')->insert($subCategory);
            }
        }

        // ここで、新しく追加した中間カテゴリのIDを取得
        $alcoholCategoryId = DB::table('categories')->where('name', 'アルコール')->first()->id;
        $softDrinkCategoryId = DB::table('categories')->where('name', 'ソフトドリンク')->first()->id;

        // アルコールとソフトドリンクのさらに下位の子カテゴリを投入
        $furtherSubCategoriesToInsert = [
            // アルコールのさらに子カテゴリ
            [
                'name' => 'ビール',
                'description' => '各種ビール',
                'parent_id' => $alcoholCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '日本酒',
                'description' => '日本各地の日本酒',
                'parent_id' => $alcoholCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '焼酎',
                'description' => '焼酎各種',
                'parent_id' => $alcoholCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'サワー・酎ハイ',
                'description' => 'サワー・酎ハイ各種',
                'parent_id' => $alcoholCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'その他アルコール',
                'description' => 'その他のアルコール飲料',
                'parent_id' => $alcoholCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ソフトドリンクのさらに子カテゴリ
            [
                'name' => 'お茶',
                'description' => '緑茶、ウーロン茶など',
                'parent_id' => $softDrinkCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '炭酸飲料',
                'description' => 'コーラ、サイダーなど',
                'parent_id' => $softDrinkCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ジュース',
                'description' => 'オレンジジュース、アップルジュースなど',
                'parent_id' => $softDrinkCategoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'その他ソフトドリンク',
                'description' => 'その他のソフトドリンク',
                'parent_id' => $softDrinkCategoryId,
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

        foreach ($furtherSubCategoriesToInsert as $subCategory) {
            if (!DB::table('categories')->where('name', $subCategory['name'])->exists()) {
                DB::table('categories')->insert($subCategory);
            }
        }
    }
}