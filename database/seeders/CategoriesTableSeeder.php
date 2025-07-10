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
        // DB::table('categories')->truncate();

        // 親カテゴリから順に投入していく
        // この後の子カテゴリの登録で必要になるため、idを先に取得しておく
        DB::table('categories')->insert([
            [
                'name' => 'ラーメン',
                'description' => '各種ラーメン',
                'parent_id' => null, // 親カテゴリ
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'サイドメニュー',
                'description' => 'ラーメンと一緒にお楽しみいただけるメニュー',
                'parent_id' => null, // 親カテゴリ
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ドリンク',
                'description' => '各種お飲み物',
                'parent_id' => null, // 親カテゴリ
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'トッピング',
                'description' => 'ラーメンに追加できる具材',
                'parent_id' => null, // 親カテゴリ
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 親カテゴリのIDを取得
        $ramenCategoryId = DB::table('categories')->where('name', 'ラーメン')->first()->id;
        $sideMenuCategoryId = DB::table('categories')->where('name', 'サイドメニュー')->first()->id;
        $drinkCategoryId = DB::table('categories')->where('name', 'ドリンク')->first()->id;
        $toppingCategoryId = DB::table('categories')->where('name', 'トッピング')->first()->id;

        // 子カテゴリを投入
        DB::table('categories')->insert([
            // ラーメンの子カテゴリ
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
        ]);
    }
}