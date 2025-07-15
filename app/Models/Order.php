<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * マスアサインメントを許可する属性
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'shop_id',
        'delivery_address',
        'total_amount',
        'delivery_address',
        'delivery_notes',
        'status',
    ];

    /**
     * モデルの配列フォームに追加される属性
     *
     * @var array<string, string>
     */
    protected $casts = [
        // status は文字列なので特にキャストは不要ですが、
        // 必要に応じてenumキャストなども検討できます（Laravel 10+）
        // 'status' => OrderStatusEnum::class, // 例
    ];

    /**
     * Order は単一の User に属する (多対一)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Order は単一の Shop に属する (多対一)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Order は複数の OrderItem を持つ (一対多)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}