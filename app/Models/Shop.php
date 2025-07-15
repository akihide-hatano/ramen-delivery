<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// ★「use Illuminate\Support\Facades\DB;」の行は削除されていることを確認してください★

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone_number',
        'business_hours',
        'description',
        'photo_1_url',
        'photo_2_url',
        'photo_3_url',
        'has_parking',
        'has_table_seats',
        'has_counter_seats',
        'accept_cash',
        'accept_credit_card',
        'accept_e_money',
        'regular_holiday',
        'lat', // ★★★追加★★★
        'lon', // ★★★追加★★★
    ];

    // ★protected $casts プロパティの中に「'location' => 'string'」の行がないことを確認してください★
    // 例えば、以下のようになっているはずです。
    // protected $casts = [
    //     // もし他のキャストがあればここに記述
    // ];

    public function products()
    {
       return $this->belongsToMany(Product::class, 'shop_products');
    }
}