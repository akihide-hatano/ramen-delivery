<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // ユーザーは他のテーブルが参照する可能性があるので、最初に配置するのが一般的
            UsersTableSeeder::class,
            // カテゴリは商品が参照するので、次に配置
            CategoriesTableSeeder::class,
            // 店舗と商品は互いに参照するが、店舗が先に必要になることが多い
            ShopsTableSeeder::class,
            ProductsTableSeeder::class,
            // 必要に応じて他のシーダーもここに追加
            ShopProductsTableSeeder::class, // ★最後に、店舗と商品の紐付けデータ★
        ]);
    }
}