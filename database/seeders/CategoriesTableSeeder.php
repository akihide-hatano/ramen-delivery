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
        // categories テーブルの display_order カラムにデフォルト値があることを前提とします。
        // もし truncate() を使う場合は、全てのデータが消えるため注意が必要です。
        // DB::table('categories')->truncate(); // 開発時のみ有効にする

        // カテゴリデータを定義（display_orderとparent_idを設定）
        $categoriesData = [
            // 最上位の親カテゴリ（display_orderで並び順を制御）
            ['name' => 'ラーメン', 'description' => '各種ラーメン', 'parent_id' => null, 'display_order' => 10],
            ['name' => 'サイドメニュー', 'description' => 'ラーメンと一緒にお楽しみいただけるメニュー', 'parent_id' => null, 'display_order' => 20],
            ['name' => 'ドリンク', 'description' => '各種お飲み物', 'parent_id' => null, 'display_order' => 30],
            ['name' => 'トッピング', 'description' => 'ラーメンに追加できる具材', 'parent_id' => null, 'display_order' => 40], // トッピングは最後に表示されるように設定
        ];

        // まず最上位の親カテゴリを挿入・更新
        foreach ($categoriesData as $category) {
            DB::table('categories')->updateOrInsert(
                ['name' => $category['name']], // name でレコードを特定
                array_merge($category, ['created_at' => now(), 'updated_at' => now()]) // 存在しない場合はinsert、存在する場合はupdate
            );
        }

        // 親カテゴリのIDを改めて取得 (updateOrInsert後なので確実にIDが取得できる)
        $ramenCategoryId = DB::table('categories')->where('name', 'ラーメン')->first()->id;
        $sideMenuCategoryId = DB::table('categories')->where('name', 'サイドメニュー')->first()->id;
        $drinkCategoryId = DB::table('categories')->where('name', 'ドリンク')->first()->id;
        $toppingCategoryId = DB::table('categories')->where('name', 'トッピング')->first()->id;

        // 子カテゴリを定義 (display_orderとparent_idを設定)
        $subCategoriesData = [
            // ラーメンの子カテゴリ
            ['name' => '塩ラーメン', 'description' => 'あっさりとした塩味のラーメン', 'parent_id' => $ramenCategoryId, 'display_order' => 11], // 塩ラーメンをラーメンの子カテゴリとして一番上に
            ['name' => '醤油ラーメン', 'description' => '伝統的な醤油ベースのラーメン', 'parent_id' => $ramenCategoryId, 'display_order' => 12],
            ['name' => '豚骨ラーメン', 'description' => '濃厚な豚骨スープのラーメン', 'parent_id' => $ramenCategoryId, 'display_order' => 13],
            ['name' => '味噌ラーメン', 'description' => '風味豊かな味噌ベースのラーメン', 'parent_id' => $ramenCategoryId, 'display_order' => 14],

            // サイドメニューの子カテゴリ
            ['name' => 'ご飯物', 'description' => 'ご飯系のサイドメニュー', 'parent_id' => $sideMenuCategoryId, 'display_order' => 21],
            ['name' => '唐揚げ', 'description' => 'ジューシーな唐揚げ', 'parent_id' => $sideMenuCategoryId, 'display_order' => 22],
            ['name' => 'チャーハン', 'description' => '香ばしいチャーハン', 'parent_id' => $sideMenuCategoryId, 'display_order' => 23],
            ['name' => '餃子', 'description' => '定番の焼き餃子', 'parent_id' => $sideMenuCategoryId, 'display_order' => 24],
            ['name' => '一品料理', 'description' => '単品で楽しめる料理', 'parent_id' => $sideMenuCategoryId, 'display_order' => 25],

            // ドリンクの子カテゴリ
            ['name' => 'アルコール', 'description' => 'アルコール飲料', 'parent_id' => $drinkCategoryId, 'display_order' => 31],
            ['name' => 'ソフトドリンク', 'description' => '清涼飲料水など', 'parent_id' => $drinkCategoryId, 'display_order' => 32],

            // トッピングの子カテゴリ
            ['name' => 'チャーシュー', 'description' => '追加の特製チャーシュー', 'parent_id' => $toppingCategoryId, 'display_order' => 41],
            ['name' => '味玉', 'description' => '半熟の味付け煮卵', 'parent_id' => $toppingCategoryId, 'display_order' => 42],
            ['name' => 'ネギ', 'description' => '新鮮な青ネギ',
                'parent_id' => $toppingCategoryId, 'display_order' => 43],
            ['name' => 'メンマ', 'description' => 'コリコリとしたメンマ', 'parent_id' => $toppingCategoryId, 'display_order' => 44],
            // 替え玉はトッピングに含める
            ['name' => '替え玉', 'description' => '追加の麺。', 'parent_id' => $toppingCategoryId, 'display_order' => 45],
        ];

        // 子カテゴリを挿入・更新
        foreach ($subCategoriesData as $subCategory) {
            DB::table('categories')->updateOrInsert(
                ['name' => $subCategory['name']],
                array_merge($subCategory, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // さらに下位のカテゴリを取得して挿入・更新
        $alcoholCategoryId = DB::table('categories')->where('name', 'アルコール')->first()->id;
        $softDrinkCategoryId = DB::table('categories')->where('name', 'ソフトドリンク')->first()->id;

        $furtherSubCategoriesData = [
            // アルコールのさらに子カテゴリ
            ['name' => 'ビール', 'description' => '各種ビール', 'parent_id' => $alcoholCategoryId, 'display_order' => 311],
            ['name' => '日本酒', 'description' => '日本各地の日本酒', 'parent_id' => $alcoholCategoryId, 'display_order' => 312],
            ['name' => '焼酎', 'description' => '焼酎各種', 'parent_id' => $alcoholCategoryId, 'display_order' => 313],
            ['name' => 'サワー・酎ハイ', 'description' => 'サワー・酎ハイ各種', 'parent_id' => $alcoholCategoryId, 'display_order' => 314],
            ['name' => 'その他アルコール', 'description' => 'その他のアルコール飲料', 'parent_id' => $alcoholCategoryId, 'display_order' => 315],

            // ソフトドリンクのさらに子カテゴリ
            ['name' => 'お茶', 'description' => '緑茶、ウーロン茶など', 'parent_id' => $softDrinkCategoryId, 'display_order' => 321],
            ['name' => '炭酸飲料', 'description' => 'コーラ、サイダーなど', 'parent_id' => $softDrinkCategoryId, 'display_order' => 322],
            ['name' => 'ジュース', 'description' => 'オレンジジュース、アップルジュースなど', 'parent_id' => $softDrinkCategoryId, 'display_order' => 323],
            ['name' => 'その他ソフトドリンク', 'description' => 'その他のソフトドリンク', 'parent_id' => $softDrinkCategoryId, 'display_order' => 324],
        ];

        foreach ($furtherSubCategoriesData as $subCategory) {
            DB::table('categories')->updateOrInsert(
                ['name' => $subCategory['name']],
                array_merge($subCategory, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}