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
        Schema::create('shop_products', function (Blueprint $table) {
            // 中間テーブルなので、idは通常不要。複合主キーを設定する。
            // shopsテーブルへの外部キー
            $table->foreignId('shop_id')
                  ->constrained() // 'shops' テーブルの 'id' を参照
                  ->onDelete('cascade'); // 親レコード削除時に子レコードも削除

            // productsテーブルへの外部キー
            $table->foreignId('product_id')
                  ->constrained() // 'products' テーブルの 'id' を参照
                  ->onDelete('cascade'); // 親レコード削除時に子レコードも削除

            // 複合主キーの設定
            // 同じ店舗に同じ商品が複数紐付けられないようにする
            $table->primary(['shop_id', 'product_id']);

            $table->timestamps(); // created_at と updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_products');
    }
};