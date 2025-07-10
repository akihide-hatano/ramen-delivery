<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'photo_1_url',
        'photo_2_url',
        'photo_3_url',
        'description',
        'phone_number',
        'has_parking',
        'business_hours',
        'regular_holiday',
    ];

    /**
     * Shop は複数の商品を持つ (一対多)
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Shop は複数の注文を持つ (一対多)
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}