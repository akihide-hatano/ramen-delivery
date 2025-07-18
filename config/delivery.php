<?php

return [
    // 基本的な準備時間 (分)
    'base_preparation_time_minutes' => 20,

    // 1kmあたりの配達時間 (分/km)
    'delivery_speed_minutes_per_km' => 3,

    // ピーク時間帯 (HH:MM-HH:MM 形式)
    'peak_hours' => [
        '12:00-13:00', // 昼食時
        '18:00-20:00', // 夕食時
    ],

    // ピーク時の追加配達時間 (分)
    'peak_surcharge_minutes' => 20,

    // 予測時間のランダムバッファ (最小・最大、分)
    'buffer_minutes_min' => 5,
    'buffer_minutes_max' => 15,

    // 事前定義された配達エリアとそれに対応する緯度・経度
    'delivery_zones' => [
        '京都市内 (中心部)' => [
            'latitude' => 35.004453,
            'longitude' => 135.767988,
        ],
        '京都市 (山科区)' => [
            'latitude' => 34.9782,
            'longitude' => 135.8117,
        ],
        '京都市 (伏見区)' => [
            'latitude' => 34.9392,
            'longitude' => 135.7681,
        ],
        '大阪市内 (梅田周辺)' => [
            'latitude' => 34.702485,
            'longitude' => 135.49595,
        ],
    ],

    // ★★★ここから追加: 希望配達時間スロットの設定★★★
    'delivery_time_slots' => [
        'ASAP' => 'できるだけ早く', // 特別な値
        '12:00' => '12:00 - 12:30',
        '12:30' => '12:30 - 13:00',
        '13:00' => '13:00 - 13:30',
        '13:30' => '13:30 - 14:00',
        '14:00' => '14:00 - 14:30',
        '14:30' => '14:30 - 15:00',
        '15:00' => '15:00 - 15:30',
        '15:30' => '15:30 - 16:00',
        '16:00' => '16:00 - 16:30',
        '16:30' => '16:30 - 17:00',
        '17:00' => '17:00 - 17:30',
        '17:30' => '17:30 - 18:00',
        '18:00' => '18:00 - 18:30',
        '18:30' => '18:30 - 19:00',
        '19:00' => '19:00 - 19:30',
        '19:30' => '19:30 - 20:00',
        '20:00' => '20:00 - 20:30',
        '20:30' => '20:30 - 21:00',
        // 必要に応じてさらに追加
    ],
];