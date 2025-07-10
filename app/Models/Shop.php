<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin', // 追加したカラムをマスアサインメント可能にする
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean', // boolean型にキャストする
        ];
    }

    /**
     * ユーザーが管理者かどうかをチェックするヘルパーメソッド
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * User は複数の注文を持つ (一対多)
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * User は一つのカートを持つ (一対一)
     */
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }
}