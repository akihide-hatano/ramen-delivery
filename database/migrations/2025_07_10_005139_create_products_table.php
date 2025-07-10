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
            //shop_idで外部キーの繋がり
            $table->foreignId('shop_id')->constrained('shops')->onDelete('cascade');
            //categry_idで外部キーの繋がり
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            // name
            $table->string('name', 100);
            // description
            $table->text('description')->nullable();
            // price
            $table->integer('price');
            // 商品画像
            $table->string('image_url', 255)->nullable();

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
