<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // name
            $table->string('name', 100);
            // description
            $table->text('description')->nullable();
            // price
            $table->integer('price');
            // 商品画像
            $table->string('image_url', 255)->nullable();
            $table->boolean('is_delivery')->default(true);
            // カテゴリID (categoriesテーブルへの外部キー)
            $table->foreignId('category_id')
                  ->constrained() // categoriesテーブルのidを参照 (規約based)
                  ->onDelete('cascade'); // カテゴリが削除されたら商品も削除
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
