<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    /**
     * マスアサインメントを許可する属性
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // カートがユーザーに紐づく場合
    ];

    /**
     * Cart は単一の User に属する (一対一)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cart は複数の CartItem を持つ (一対多)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}