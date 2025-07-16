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
        'is_delivery',
    ];

    /**
     * 商品が属するカテゴリを取得
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 商品が属する店舗を取得
     * ★追加: このリレーションが不足していました
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * 商品を取り扱っている店舗を取得 (多対多)
     * ★ここを修正します: メソッド名を 'shop' から 'shops' (複数形) に変更し、belongsToMany を使用★
     */
    public function shops() // メソッド名を複数形 'shops' に変更
    {
        // 第二引数は中間テーブル名、第三引数はProductモデルの外部キー、第四引数はShopモデルの外部キー
        return $this->belongsToMany(Shop::class, 'shop_products', 'product_id', 'shop_id');
    }

    /**
     * 商品がアルコール飲料であるかを判定する
     * @return bool
     */
    public function isAlcohol(): bool
    {
        // Categoryリレーションがロードされていることを確認
        if (!$this->relationLoaded('category') || !$this->category) {
            // カテゴリがロードされていない場合は、リロードするか、直接DBをクエリ
            // ここではシンプルに、カテゴリがなければfalseとする
            return false;
        }

        // アルコール関連のカテゴリ名を定義
        $alcoholCategoryNames = [
            'アルコール', // 親カテゴリ
            'ビール',
            '日本酒',
            '焼酎',
            'サワー・酎ハイ',
            'その他アルコール',
        ];

        // 現在の商品のカテゴリ名、およびその親カテゴリ名がアルコール関連かどうかをチェック
        $currentCategoryName = $this->category->name;
        $parentCategoryName = $this->category->parent ? $this->category->parent->name : null;

        return in_array($currentCategoryName, $alcoholCategoryNames) ||
               ($parentCategoryName && in_array($parentCategoryName, $alcoholCategoryNames));
    }
}