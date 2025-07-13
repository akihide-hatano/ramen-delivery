<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; // DBファサードをuseする

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
        'location', // PostGISのlocationカラム
    ];

    // productsとのリレーションを定義
    public function products()
    {
       return $this->belongsToMany(Product::class, 'shop_products');
    }

    // ★★★ここからアクセサを追加★★★
    /**
     * Get the latitude from the location attribute.
     *
     * @return float|null
     */
    public function getLatAttribute(): ?float
    {
        // locationカラムがnullでない、かつPostGISのST_Y関数が利用可能なら緯度を取得
        if ($this->location) {
            // ST_Y(location::geometry) を使って緯度を抽出
            // DB::raw() を使うことで、EloquentがSQL関数として認識する
            $latitude = DB::selectOne("SELECT ST_Y(?::geometry) AS lat", [$this->location])->lat;
            return (float) $latitude;
        }
        return null;
    }

    /**
     * Get the longitude from the location attribute.
     *
     * @return float|null
     */
    public function getLonAttribute(): ?float
    {
        // locationカラムがnullでない、かつPostGISのST_X関数が利用可能なら経度を取得
        if ($this->location) {
            // ST_X(location::geometry) を使って経度を抽出
            // DB::raw() を使うことで、EloquentがSQL関数として認識する
            $longitude = DB::selectOne("SELECT ST_X(?::geometry) AS lon", [$this->location])->lon;
            return (float) $longitude;
        }
        return null;
    }
    // ★★★アクセサここまで★★★
}