<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    /**
     * cart_items テーブルには created_at/updated_at がないため、無効にする
     *
     * @var bool
     */
    public $timestamps = false; // マイグレーションでtimestamps()を定義していないため

    /**
     * マスアサインメントを許可する属性
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    /**
     * CartItem は単一の Cart に属する (多対一)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * CartItem は単一の Product に属する (多対一)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}