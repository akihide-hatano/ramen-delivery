<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // DBファサードをuseする

class ShopsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存データをクリアしてから新しいデータを投入する場合（開発環境向け）
        DB::table('shops')->truncate(); // ★重要★ 既存のデータをクリアしないと重複エラーやlocationがNULLのままになります

        DB::table('shops')->insert([
            [
                'name' => 'ラーメン潮屋 大阪難波店',
                'address' => '大阪府大阪市中央区難波',
                'phone_number' => '06-1111-2222',
                'photo_1_url' => 'https://example.com/ushioya_namba_1.jpg',
                'photo_2_url' => null,
                'photo_3_url' => null,
                'description' => '魚介系のあっさりスープが人気の潮屋、難波の中心地で営業中！',
                'has_parking' => false,
                'has_table_seats' => true,
                'has_counter_seats' => true,
                'business_hours' => '11:00-23:00',
                'regular_holiday' => null,
                'accept_cash' => true,
                'accept_credit_card' => true,
                'accept_e_money' => true,
                'created_at' => now(),
                'updated_at' => now(),
                // ★追加: locationカラムにデータを挿入★
                // 仮の緯度・経度。実際の場所に合わせて修正してください。
                // ST_MakePoint(経度, 緯度) の順なので注意！
                'location' => DB::raw("ST_SetSRID(ST_MakePoint(135.5000, 34.6667), 4326)::geography"), // 例: 難波駅周辺
            ],
            [
                'name' => 'ラーメン潮屋 梅田店',
                'address' => '大阪府大阪市北区梅田',
                'phone_number' => '06-3333-4444',
                'photo_1_url' => 'https://example.com/ushioya_umeda_1.jpg',
                'photo_2_url' => null,
                'photo_3_url' => null,
                'description' => '大阪梅田の地下街にある潮屋。ショッピングの合間にも立ち寄れます。',
                'has_parking' => false,
                'has_table_seats' => true,
                'has_counter_seats' => true,
                'business_hours' => '11:00-22:30',
                'regular_holiday' => '不定休',
                'accept_cash' => true,
                'accept_credit_card' => true,
                'accept_e_money' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'location' => DB::raw("ST_SetSRID(ST_MakePoint(135.4950, 34.7020), 4326)::geography"), // 例: 梅田駅周辺
            ],
            [
                'name' => 'ラーメン潮屋 芝田店',
                'address' => '大阪府大阪市北区芝田',
                'phone_number' => '06-5555-6666',
                'photo_1_url' => 'https://example.com/ushioya_shibata_1.jpg',
                'photo_2_url' => null,
                'photo_3_url' => null,
                'description' => '芝田エリアのオフィス街に位置。ランチタイムに最適です。',
                'has_parking' => false,
                'has_table_seats' => true,
                'has_counter_seats' => true,
                'business_hours' => '11:00-15:00, 17:00-22:00',
                'regular_holiday' => '土日祝',
                'accept_cash' => true,
                'accept_credit_card' => true,
                'accept_e_money' => false,
                'created_at' => now(),
                'updated_at' => now(),
                'location' => DB::raw("ST_SetSRID(ST_MakePoint(135.4970, 34.7060), 4326)::geography"), // 例: 芝田周辺
            ],
            // --- ここからラーメン潮屋 京都の店舗を追加 ---
            // 現在地を考慮し、京都市内の店舗にはより正確な緯度経度を設定すると良いでしょう
            [
                'name' => 'ラーメン潮屋 河原町三条店',
                'address' => '京都府京都市中京区河原町通三条下る',
                'phone_number' => '075-7777-1111',
                'photo_1_url' => 'https://example.com/ushioya_kawasanjyo_1.jpg',
                'photo_2_url' => null,
                'photo_3_url' => null,
                'description' => '河原町三条の中心部にある潮屋。観光客にも人気です。',
                'has_parking' => false,
                'has_table_seats' => true,
                'has_counter_seats' => true,
                'business_hours' => '11:00-22:00',
                'regular_holiday' => null,
                'accept_cash' => true,
                'accept_credit_card' => true,
                'accept_e_money' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'location' => DB::raw("ST_SetSRID(ST_MakePoint(135.7712, 35.0089), 4326)::geography"), // 例: 河原町三条交差点
            ],
            [
                'name' => 'ラーメン潮屋 河原町四条店',
                'address' => '京都府京都市下京区河原町通四条上る',
                'phone_number' => '075-7777-2222',
                'photo_1_url' => 'https://example.com/ushioya_kawashijo_1.jpg',
                'photo_2_url' => null,
                'photo_3_url' => null,
                'description' => '京都最大の繁華街、四条河原町にある潮屋の店舗。',
                'has_parking' => false,
                'has_table_seats' => true,
                'has_counter_seats' => true,
                'business_hours' => '11:00-23:00',
                'regular_holiday' => null,
                'accept_cash' => true,
                'accept_credit_card' => true,
                'accept_e_money' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'location' => DB::raw("ST_SetSRID(ST_MakePoint(135.7699, 35.0039), 4326)::geography"), // 例: 河原町四条交差点
            ],
            [
                'name' => 'ラーメン潮屋 大宮店',
                'address' => '京都府京都市中京区大宮通御池下る',
                'phone_number' => '075-7777-3333',
                'photo_1_url' => 'https://example.com/ushioya_omiya_1.jpg',
                'photo_2_url' => null,
                'photo_3_url' => null,
                'description' => '地域密着型の大宮店。地元の皆様にご愛顧いただいております。',
                'has_parking' => true,
                'has_table_seats' => true,
                'has_counter_seats' => true,
                'business_hours' => '11:30-14:30, 17:30-21:00',
                'regular_holiday' => '水曜日',
                'accept_cash' => true,
                'accept_credit_card' => false,
                'accept_e_money' => false,
                'created_at' => now(),
                'updated_at' => now(),
                'location' => DB::raw("ST_SetSRID(ST_MakePoint(135.7533, 35.0069), 4326)::geography"), // 例: 大宮御池交差点
            ],
            [
                'name' => 'ラーメン潮屋 京都駅店',
                'address' => '京都府京都市下京区東塩小路町',
                'phone_number' => '075-7777-4444',
                'photo_1_url' => 'https://example.com/ushioya_kyoto_st_1.jpg',
                'photo_2_url' => null,
                'photo_3_url' => null,
                'description' => '京都駅構内にあるので、旅行の際にも立ち寄りやすい店舗です。',
                'has_parking' => false,
                'has_table_seats' => true,
                'has_counter_seats' => false,
                'business_hours' => '10:00-22:00',
                'regular_holiday' => null,
                'accept_cash' => true,
                'accept_credit_card' => true,
                'accept_e_money' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'location' => DB::raw("ST_SetSRID(ST_MakePoint(135.7588, 34.9856), 4326)::geography"), // 例: 京都駅中央口付近
            ],
            [
                'name' => 'ラーメン潮屋 烏丸店',
                'address' => '京都府京都市中京区烏丸通蛸薬師下る',
                'phone_number' => '075-7777-5555',
                'photo_1_url' => 'https://example.com/ushioya_karasuma_1.jpg',
                'photo_2_url' => null,
                'photo_3_url' => null,
                'description' => 'ビジネス街に位置する烏丸店。ランチや仕事帰りの一杯にも。',
                'has_parking' => false,
                'has_table_seats' => true,
                'has_counter_seats' => true,
                'business_hours' => '11:00-15:00, 17:00-22:00',
                'regular_holiday' => '土日祝',
                'accept_cash' => true,
                'accept_credit_card' => true,
                'accept_e_money' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'location' => DB::raw("ST_SetSRID(ST_MakePoint(135.7621, 35.0069), 4326)::geography"), // 例: 烏丸御池駅周辺
            ],
            [
                'name' => 'ラーメン潮屋 烏丸七条店',
                'address' => '京都府京都市下京区烏丸通七条下る',
                'phone_number' => '075-7777-6666',
                'photo_1_url' => 'https://example.com/ushioya_karasuma7_1.jpg',
                'photo_2_url' => null,
                'photo_3_url' => null,
                'description' => '京都タワーからも近い烏丸七条の交差点にあります。',
                'has_parking' => true,
                'has_table_seats' => true,
                'has_counter_seats' => true,
                'business_hours' => '11:00-22:00',
                'regular_holiday' => null,
                'accept_cash' => true,
                'accept_credit_card' => true,
                'accept_e_money' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'location' => DB::raw("ST_SetSRID(ST_MakePoint(135.7617, 34.9930), 4326)::geography"), // 例: 烏丸七条交差点
            ],
        ]);
    }
}