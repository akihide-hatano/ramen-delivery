<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category; // Categoryモデルをインポート
use App\Models\Shop;     // ★Shopモデルをインポート

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 開発時に全商品をクリア
        DB::table('products')->truncate();

        // 必要なカテゴリIDを取得
        $shoyuRamenId = DB::table('categories')->where('name', '醤油ラーメン')->first()->id;
        $tonkotsuRamenId = DB::table('categories')->where('name', '豚骨ラーメン')->first()->id;
        $misoRamenId = DB::table('categories')->where('name', '味噌ラーメン')->first()->id;
        $shioRamenId = DB::table('categories')->where('name', '塩ラーメン')->first()->id;

        $karaageId = DB::table('categories')->where('name', '唐揚げ')->first()->id;
        $chahanId = DB::table('categories')->where('name', 'チャーハン')->first()->id;
        $gohanmonoId = DB::table('categories')->where('name', 'ご飯物')->first()->id;
        $ippinryoriId = DB::table('categories')->where('name', '一品料理')->first()->id;
        $gyozaId = DB::table('categories')->where('name', '餃子')->first()->id;

        $beerId = DB::table('categories')->where('name', 'ビール')->first()->id;
        $nihonshuId = DB::table('categories')->where('name', '日本酒')->first()->id;
        $shochuId = DB::table('categories')->where('name', '焼酎')->first()->id;
        $sourChuhaiId = DB::table('categories')->where('name', 'サワー・酎ハイ')->first()->id;
        $otherAlcoholId = DB::table('categories')->where('name', 'その他アルコール')->first()->id;

        $ochaId = DB::table('categories')->where('name', 'お茶')->first()->id;
        $tansanInryoId = DB::table('categories')->where('name', '炭酸飲料')->first()->id;
        $juiceId = DB::table('categories')->where('name', 'ジュース')->first()->id;
        $otherSoftDrinkId = DB::table('categories')->where('name', 'その他ソフトドリンク')->first()->id;

        $toppingCategoryId = DB::table('categories')->where('name', 'トッピング')->first()->id;


        // ★Shopモデルから正式な店舗名を取得し、変数に格納
        // ShopsTableSeederで定義されている正確な店舗名を使用してください
        $nambaShopName = Shop::where('name', 'ラーメン潮屋 大阪難波店')->first()->name;
        $umedaShopName = Shop::where('name', 'ラーメン潮屋 梅田店')->first()->name;
        $shibataShopName = Shop::where('name', 'ラーメン潮屋 芝田店')->first()->name;
        $kawaSanjoShopName = Shop::where('name', 'ラーメン潮屋 河原町三条店')->first()->name;
        $kawaShijoShopName = Shop::where('name', 'ラーメン潮屋 河原町四条店')->first()->name;
        $kyotoEkiShopName = Shop::where('name', 'ラーメン潮屋 京都駅店')->first()->name;
        $omiyaShopName = Shop::where('name', 'ラーメン潮屋 大宮店')->first()->name;
        $karasumaShopName = Shop::where('name', 'ラーメン潮屋 烏丸店')->first()->name; // 烏丸店を追加
        $karasumaShichijoShopName = Shop::where('name', 'ラーメン潮屋 烏丸七条店')->first()->name;


        // --- アプリケーション全体でユニークな商品マスターデータを定義 ---
        $productsData = [
            // ラーメン（共通商品）
            [
                'category_id' => $shioRamenId,
                'name' => '潮屋塩ラーメン',
                'description' => '魚介系のあっさりとした特製塩スープが自慢の基本メニュー。',
                'price' => 880,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Shioya+Base+Ramen',
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null, // 共通商品なのでnull
            ],
            [
                'category_id' => $shioRamenId,
                'name' => '味玉潮屋塩ラーメン',
                'description' => '潮屋塩ラーメンに特製味玉をトッピング。',
                'price' => 980,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Ajitama+Shio+Ramen',
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null, // 共通商品なのでnull
            ],
            // ★限定商品に is_limited => true と limited_location を正式な店舗名で設定★
            [
                'category_id' => $shioRamenId,
                'name' => '焦がし醤油塩ラーメン',
                'description' => '焦がし醤油の香ばしさが際立つ、難波店限定の塩ラーメン。',
                'price' => 950,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Namba+Kogashi+Shio',
                'is_limited' => true,
                'limited_location' => $nambaShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $shioRamenId,
                'name' => 'バターコーン塩ラーメン',
                'description' => '北海道産バターと甘いコーンでまろやかに仕上げた塩ラーメン。',
                'price' => 1000,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Butter+Corn+Shio',
                'is_limited' => true,
                'limited_location' => $shibataShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $shioRamenId,
                'name' => '梅しそ塩ラーメン',
                'description' => '紀州梅と大葉でさっぱりと仕上げた、梅田店限定の塩ラーメン。',
                'price' => 960,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Umeda+Ume+Shiso+Shio',
                'is_limited' => true,
                'limited_location' => $umedaShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $shioRamenId,
                'name' => '背脂こってり塩ラーメン',
                'description' => '塩ベースに背脂を加え、こってり感を増した一杯。',
                'price' => 990,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Seabura+Shio',
                'is_limited' => true,
                'limited_location' => $umedaShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $shioRamenId,
                'name' => 'レモン塩ラーメン',
                'description' => 'フレッシュレモンを絞っていただく、爽やかな塩ラーメン。',
                'price' => 950,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Shibata+Lemon+Shio',
                'is_limited' => true,
                'limited_location' => $shibataShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $shioRamenId,
                'name' => 'ピリ辛坦々塩ラーメン',
                'description' => '塩スープにラー油と肉味噌でピリ辛に仕上げた坦々風塩ラーメン。',
                'price' => 1020,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Tantan+Shio',
                'is_limited' => true,
                'limited_location' => $karasumaShichijoShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $shioRamenId,
                'name' => '柚子塩ラーメン',
                'description' => '柚子の香りが広がる、京都らしい上品な塩ラーメン。',
                'price' => 970,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=KawaSanjo+Yuzu+Shio',
                'is_limited' => true,
                'limited_location' => $kawaSanjoShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $shioRamenId,
                'name' => '鶏白湯塩ラーメン',
                'description' => '濃厚な鶏白湯スープを塩ベースで仕上げたコク深い一杯。',
                'price' => 1050,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Toripaitan+Shio',
                'is_limited' => true,
                'limited_location' => $kawaSanjoShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $shioRamenId,
                'name' => '焦がしネギ塩ラーメン',
                'description' => '香ばしい焦がしネギの風味が食欲をそそる塩ラーメン。',
                'price' => 980,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Shijo+Kogashi+Negi+Shio',
                'is_limited' => true,
                'limited_location' => $kawaShijoShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $shioRamenId,
                'name' => '京都九条ネギ塩ラーメン',
                'description' => '京都九条ネギをふんだんに使った、風味豊かな塩ラーメン。',
                'price' => 1000,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Kujo+Negi+Shio',
                'is_limited' => true,
                'limited_location' => $kyotoEkiShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $shioRamenId,
                'name' => '焦がしにんにく塩ラーメン',
                'description' => 'ガツンと効いた焦がしにんにくが特徴の塩ラーメン。',
                'price' => 990,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Omiya+Garlic+Shio',
                'is_limited' => true,
                'limited_location' => $omiyaShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $shioRamenId,
                'name' => '背脂味噌塩ラーメン',
                'description' => '塩スープに背脂と少量の味噌を加え、深みを出した限定ラーメン。',
                'price' => 1030,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Omiya+Miso+Shio',
                'is_limited' => true,
                'limited_location' => $omiyaShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $shioRamenId,
                'name' => '京鴨塩ラーメン',
                'description' => '京鴨の出汁が効いた、京都駅店限定の贅沢な塩ラーメン。',
                'price' => 1100,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Kyoto+Duck+Shio',
                'is_limited' => true,
                'limited_location' => $kyotoEkiShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $shioRamenId,
                'name' => 'とろろ昆布塩ラーメン',
                'description' => '磯の香りととろみが塩ラーメンと絶妙に絡む一杯。',
                'price' => 960,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Tororo+Kombu+Shio',
                'is_limited' => true,
                'limited_location' => $kyotoEkiShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $shioRamenId,
                'name' => '海老塩ラーメン',
                'description' => '海老の旨味が凝縮された、香ばしい塩ラーメン。',
                'price' => 1020,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Karasuma+Shrimp+Shio',
                'is_limited' => true,
                'limited_location' => $karasumaShichijoShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $shioRamenId,
                'name' => '塩豚骨ラーメン',
                'description' => '濃厚豚骨スープを塩味でさっぱりと仕上げた一杯。',
                'price' => 980,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Shio+Tonkotsu',
                'is_limited' => true,
                'limited_location' => $nambaShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $shioRamenId,
                'name' => 'アサリ塩ラーメン',
                'description' => 'アサリの出汁が効いた、魚介の旨味あふれる塩ラーメン。',
                'price' => 1000,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Shichijo+Clam+Shio',
                'is_limited' => true,
                'limited_location' => $karasumaShichijoShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $shioRamenId,
                'name' => '辛味噌塩ラーメン',
                'description' => '塩スープに自家製辛味噌を溶かした、ピリ辛の一杯。',
                'price' => 990,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Spicy+Miso+Shio',
                'is_limited' => true,
                'limited_location' => $umedaShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],

            // サイドメニュー（共通商品）
            [
                'category_id' => $chahanId,
                'name' => '半チャーハン',
                'description' => 'ラーメンと相性抜群のミニチャーハン。',
                'price' => 400,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Half+Chahan',
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null,
            ],
            [
                'category_id' => $gohanmonoId,
                'name' => 'ライス',
                'description' => 'ご飯単品。大盛りもできます。',
                'price' => 150,
                'image_url' => null,
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null,
            ],
            [
                'category_id' => $gohanmonoId,
                'name' => 'ミニチャーシュー丼',
                'description' => '特製チャーシューが乗ったご飯。',
                'price' => 380,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Mini+Chashu+Don',
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null,
            ],
            [
                'category_id' => $karaageId,
                'name' => '鶏の唐揚げ（3個）',
                'description' => '外はカリッと中はジューシー。',
                'price' => 350,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Karaage',
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null,
            ],
            [
                'category_id' => $gyozaId,
                'name' => '特製餃子（5個）',
                'description' => '肉汁あふれる特製餃子。',
                'price' => 450,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Gyoza',
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null,
            ],
            [
                'category_id' => $ippinryoriId,
                'name' => 'ピリ辛メンマ',
                'description' => '箸休めに最適なピリ辛メンマ。',
                'price' => 280,
                'image_url' => null,
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null,
            ],
            // ★限定サイドメニュー・ドリンクにも is_limited => true と limited_location を正式な店舗名で設定★
            [
                'category_id' => $ippinryoriId,
                'name' => 'たこ焼き（3個）',
                'description' => '大阪名物たこ焼きをサイドメニューに。',
                'price' => 300,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Takoyaki',
                'is_limited' => true,
                'limited_location' => $nambaShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $gohanmonoId,
                'name' => 'ミニカレー丼',
                'description' => 'スパイシーなミニカレー丼。',
                'price' => 450,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Mini+Curry+Don',
                'is_limited' => true,
                'limited_location' => $umedaShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $ippinryoriId,
                'name' => '京風だし巻き卵',
                'description' => 'ふわふわの京風だし巻き卵。',
                'price' => 500,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Dashimaki+Tamago',
                'is_limited' => true,
                'limited_location' => $kawaSanjoShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $ippinryoriId,
                'name' => '鶏皮ポン酢',
                'description' => 'お酒が進む鶏皮ポン酢。',
                'price' => 380,
                'image_url' => null,
                'is_limited' => true,
                'limited_location' => $omiyaShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $gohanmonoId,
                'name' => '九条ネギご飯',
                'description' => '九条ネギをたっぷり乗せたご飯。',
                'price' => 300,
                'image_url' => null,
                'is_limited' => true,
                'limited_location' => $kyotoEkiShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $gohanmonoId,
                'name' => '炙りチャーシュー丼',
                'description' => '香ばしく炙ったチャーシューが乗った丼。',
                'price' => 500,
                'image_url' => 'https://placehold.co/400x300/E0E0E0/000000?text=Aburi+Chashu+Don',
                'is_limited' => true,
                'limited_location' => $karasumaShichijoShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            // ドリンク（共通商品）
            [
                'category_id' => $beerId,
                'name' => '生ビール（中ジョッキ）',
                'description' => '冷たい生ビール。',
                'price' => 500,
                'image_url' => null,
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null,
            ],
            [
                'category_id' => $ochaId,
                'name' => '烏龍茶',
                'description' => 'さっぱりとしたウーロン茶。',
                'price' => 200,
                'image_url' => null,
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null,
            ],
            [
                'category_id' => $tansanInryoId,
                'name' => 'コカ・コーラ',
                'description' => '定番のコーラ。',
                'price' => 220,
                'image_url' => null,
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null,
            ],
            [
                'category_id' => $juiceId,
                'name' => 'オレンジジュース',
                'description' => '100%オレンジジュース。',
                'price' => 220,
                'image_url' => null,
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null,
            ],
            [
                'category_id' => $nihonshuId,
                'name' => '地酒（冷）',
                'description' => '季節限定の地酒。',
                'price' => 600,
                'image_url' => null,
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null,
            ],
            [
                'category_id' => $sourChuhaiId,
                'name' => 'レモンサワー',
                'description' => '爽やかなレモンサワー。',
                'price' => 400,
                'image_url' => null,
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null,
            ],
            [
                'category_id' => $ochaId,
                'name' => '緑茶',
                'description' => '食事に合う緑茶。',
                'price' => 200,
                'image_url' => null,
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null,
            ],
            // ★限定ドリンクにも is_limited => true と limited_location を正式な店舗名で設定★
            [
                'category_id' => $ochaId,
                'name' => '特選ほうじ茶',
                'description' => '食後にぴったりの香ばしいほうじ茶。',
                'price' => 250,
                'image_url' => null,
                'is_limited' => true,
                'limited_location' => $shibataShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $tansanInryoId,
                'name' => 'クラフトコーラ',
                'description' => 'こだわりのスパイスを使ったクラフトコーラ。',
                'price' => 350,
                'image_url' => null,
                'is_limited' => true,
                'limited_location' => $kawaShijoShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],
            [
                'category_id' => $otherSoftDrinkId,
                'name' => '自家製ジンジャーエール',
                'description' => '生姜が効いた自家製ジンジャーエール。',
                'price' => 380,
                'image_url' => null,
                'is_limited' => true,
                'limited_location' => $karasumaShichijoShopName, // ★正式な店舗名を使用★
                'limited_type' => 'location',
            ],

            // トッピング（共通商品）
            [
                'category_id' => $toppingCategoryId,
                'name' => '替え玉',
                'description' => '追加の麺。',
                'price' => 150,
                'image_url' => null,
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null,
            ],
            [
                'category_id' => $toppingCategoryId,
                'name' => '特製味玉',
                'description' => 'とろーり半熟の味付け卵。',
                'price' => 120,
                'image_url' => null,
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null,
            ],
            [
                'category_id' => $toppingCategoryId,
                'name' => '追加チャーシュー（3枚）',
                'description' => 'とろとろの自家製チャーシュー。',
                'price' => 250,
                'image_url' => null,
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null,
            ],
            [
                'category_id' => $toppingCategoryId,
                'name' => '九条ネギ増し',
                'description' => '香りの良い九条ネギをたっぷり。',
                'price' => 100,
                'image_url' => null,
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null,
            ],
            [
                'category_id' => $toppingCategoryId,
                'name' => 'メンマ増し',
                'description' => 'コリコリ食感のメンマを増量。',
                'price' => 100,
                'image_url' => null,
                'is_limited' => false,
                'limited_location' => null,
                'limited_type' => null,
            ],
        ];

        foreach ($productsData as $productData) {
            Product::updateOrInsert(
                ['name' => $productData['name']],
                array_merge($productData, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}