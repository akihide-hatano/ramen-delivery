<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'category_id',
        'name',
        'description',
        'price',
        'image_url',
    ];

    /**
     * Product は単一の Shop に属する (多対一)
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
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