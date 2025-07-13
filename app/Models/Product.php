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
     * 商品が属するカテゴリを取得
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
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