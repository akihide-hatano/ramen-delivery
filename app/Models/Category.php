<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'parent_id', // parent_id もマスアサインメント可能にする
    ];

    /**
     * Category は親カテゴリを持つ (多対一、自己参照)
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Category は子カテゴリを持つ (一対多、自己参照)
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Category は複数の Product を持つ (一対多)
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // カテゴリの階層名を取得するヘルパーメソッド
    public function getHierarchicalName($separator = ' > ')
    {
        $names = [];
        $current = $this;
        while ($current) {
            array_unshift($names, $current->name); // 先頭に追加
            $current = $current->parent; // 親をたどる
        }
        return implode($separator, $names);
    }
}