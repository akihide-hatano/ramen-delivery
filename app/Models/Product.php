<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image_url',
        'is_limited',
        'limited_location',
    ];
    /**
     * Product は複数の Shop に属する (多対多)
     * ★このメソッドを新規追加★
     */
    public function shops()
    {
        // 'shop_products' は中間テーブルの名前です。
        // もし中間テーブル名が 'product_shop' など異なる場合は適宜変更してください。
        return $this->belongsToMany(Shop::class, 'shop_products');
    }

    /**
     * Product は単一の Category に属する (多対一)
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Product は複数の OrderItem に含まれる (一対多)
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Product は複数の CartItem に含まれる (一対多)
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}