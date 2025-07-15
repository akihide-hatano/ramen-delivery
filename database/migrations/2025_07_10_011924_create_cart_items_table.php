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
        Schema::create('cart_items', function (Blueprint $table) {
            // id <BIGSERIAL, NOT NULL, PK>
            $table->id();

            $table->foreignId('cart_id')->constrained('carts')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            // quantity
            $table->integer('quantity');
            //unit_price
            $table->integer('unit_price'); 
            // 同じカート内で同じ商品が複数回追加されることを防ぎ、数量を更新する仕組みにする場合
            // このユニーク制約を追加することで、同じ商品がカートに複数行登録されるのを防ぎます。
            // アプリケーション側で、既に商品が存在する場合はquantityを更新するロジックが必要になります。
            $table->unique(['cart_id', 'product_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};