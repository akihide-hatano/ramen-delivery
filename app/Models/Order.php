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
        'delivery_phone', // ★追加: 電話番号
        'delivery_zone_name', // ★追加: 配達エリア名
        'desired_delivery_time_slot', // ★修正: desired_delivery_time から変更
        'delivery_notes',
        'total_price', // ★修正: total_amount から変更
        'delivery_fee', // ★追加: 配送料
        'grand_total', // ★追加: 最終合計金額
        'payment_method', // ★追加: 支払い方法
        'status',
    ];

    /**
     * モデルの配列フォームに追加される属性
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'desired_delivery_time_slot' は文字列なので 'datetime' キャストは不要です。
        // もし実際の配達日時（タイムスタンプ）を保存する場合は 'datetime' にします。
        // 現状はスロット名（例: "ASAP", "18:00-19:00"）を保存するため、キャストは不要です。
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