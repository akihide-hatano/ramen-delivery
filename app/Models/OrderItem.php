<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * order_items テーブルには timestamps() がないため、無効にする必要があります。
     * この行は非常に重要です。
     *
     * @var bool
     */
    public $timestamps = false; // created_at と updated_at カラムがないため

    /**
     * マスアサインメントを許可する属性。
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    /**
     * OrderItem は単一の Order に属する (多対一)。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * OrderItem は単一の Product に属する (多対一)。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}