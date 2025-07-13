<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Shop;    // Shopモデルをuse
use App\Models\Product; // Productモデルをuse

class ShopProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 開発時に中間テーブルのデータをクリアする場合 (必要に応じてコメントアウトを外す)
        // DB::table('shop_products')->truncate();

        // 全ての店舗と商品を取得
        $allShops = Shop::all();
        $allProducts = Product::all();

        // 中間テーブルに挿入するデータを格納する配列
        $shopProductsToInsert = [];

        // --- 共通商品の紐付け ---
        // ラーメン潮屋の店舗に共通で提供される商品名リスト
        // ProductsTableSeederで定義したユニークな商品名と一致させる
        $commonProductNames = [
            '潮屋塩ラーメン',
            '味玉潮屋塩ラーメン',
            '半チャーハン',
            'ライス',
            'ミニチャーシュー丼',
            '鶏の唐揚げ（3個）',
            '特製餃子（5個）',
            'ピリ辛メンマ',
            '生ビール（中ジョッキ）',
            '烏龍茶',
            'コカ・コーラ',
            'オレンジジュース',
            '地酒（冷）',
            'レモンサワー',
            '緑茶',
            '替え玉',
            '特製味玉',
            '追加チャーシュー（3枚）',
            '九条ネギ増し',
            'メンマ増し',
        ];

        // ラーメン潮屋の店舗IDのみを抽出
        $ushioyaShops = $allShops->filter(function ($shop) {
            return str_contains($shop->name, 'ラーメン潮屋');
        });

        foreach ($ushioyaShops as $shop) {
            foreach ($commonProductNames as $productName) {
                $product = $allProducts->where('name', $productName)->first();
                if ($product) {
                    $shopProductsToInsert[] = [
                        'shop_id' => $shop->id,
                        'product_id' => $product->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // --- 各店舗に個別の「塩ラーメン味変」商品を追加 ---
        foreach ($allShops as $shop) {
            $shopName = $shop->name;
            $shopId = $shop->id;

            $specificProductNamesForThisShop = [];

            if (str_contains($shopName, '大阪難波店')) {
                $specificProductNamesForThisShop = [
                    '難波限定！焦がし醤油塩ラーメン',
                    'バターコーン塩ラーメン',
                ];
            } elseif (str_contains($shopName, '梅田店')) {
                $specificProductNamesForThisShop = [
                    '梅田特製！梅しそ塩ラーメン',
                    '背脂こってり塩ラーメン',
                ];
            } elseif (str_contains($shopName, '芝田店')) {
                $specificProductNamesForThisShop = [
                    '芝田限定！レモン塩ラーメン',
                    'ピリ辛坦々塩ラーメン',
                ];
            } elseif (str_contains($shopName, '河原町三条店')) {
                $specificProductNamesForThisShop = [
                    '河原町三条限定！柚子塩ラーメン',
                    '鶏白湯塩ラーメン',
                ];
            } elseif (str_contains($shopName, '河原町四条店')) {
                $specificProductNamesForThisShop = [
                    '四条限定！焦がしネギ塩ラーメン',
                    '京都九条ネギ塩ラーメン',
                ];
            } elseif (str_contains($shopName, '大宮店')) {
                $specificProductNamesForThisShop = [
                    '大宮限定！焦がしにんにく塩ラーメン',
                    '背脂味噌塩ラーメン',
                ];
            } elseif (str_contains($shopName, '京都駅店')) {
                $specificProductNamesForThisShop = [
                    '京都駅限定！京鴨塩ラーメン',
                    'とろろ昆布塩ラーメン',
                ];
            } elseif (str_contains($shopName, '烏丸店')) {
                $specificProductNamesForThisShop = [
                    '烏丸限定！海老塩ラーメン',
                    '塩豚骨ラーメン',
                ];
            } elseif (str_contains($shopName, '烏丸七条店')) {
                $specificProductNamesForThisShop = [
                    '七条限定！アサリ塩ラーメン',
                    '辛味噌塩ラーメン',
                ];
            }
            // サイドメニューやドリンクの店舗限定品もここに追加
            elseif (str_contains($shopName, '大阪難波店')) { // たこ焼き
                $specificProductNamesForThisShop[] = 'たこ焼き（3個）';
            } elseif (str_contains($shopName, '梅田店')) { // ミニカレー丼
                $specificProductNamesForThisShop[] = '梅田限定！ミニカレー丼';
            } elseif (str_contains($shopName, '芝田店')) { // 特選ほうじ茶
                $specificProductNamesForThisShop[] = '特選ほうじ茶';
            } elseif (str_contains($shopName, '河原町三条店')) { // 京風だし巻き卵
                $specificProductNamesForThisShop[] = '京風だし巻き卵';
            } elseif (str_contains($shopName, '河原町四条店')) { // クラフトコーラ
                $specificProductNamesForThisShop[] = 'クラフトコーラ';
            } elseif (str_contains($shopName, '大宮店')) { // 鶏皮ポン酢
                $specificProductNamesForThisShop[] = '大宮名物！鶏皮ポン酢';
            } elseif (str_contains($shopName, '京都駅店')) { // 九条ネギご飯
                $specificProductNamesForThisShop[] = '京都駅限定！九条ネギご飯';
            } elseif (str_contains($shopName, '烏丸店')) { // 自家製ジンジャーエール
                $specificProductNamesForThisShop[] = '自家製ジンジャーエール';
            } elseif (str_contains($shopName, '烏丸七条店')) { // 炙りチャーシュー丼
                $specificProductNamesForThisShop[] = '炙りチャーシュー丼';
            }


            foreach ($specificProductNamesForThisShop as $productName) {
                $product = $allProducts->where('name', $productName)->first();
                if ($product) {
                    // 複合主キーなので、重複挿入防止はDB側で担保されるが、シーダーとしては念のためチェック
                    $exists = DB::table('shop_products')
                                ->where('shop_id', $shopId)
                                ->where('product_id', $product->id)
                                ->exists();
                    if (!$exists) {
                        $shopProductsToInsert[] = [
                            'shop_id' => $shopId,
                            'product_id' => $product->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        }

        // 全ての紐付けデータを一括挿入
        // insertOrIgnore() を使うことで、複合主キーによる重複エラーを無視できる
        DB::table('shop_products')->insertOrIgnore($shopProductsToInsert);
    }
}