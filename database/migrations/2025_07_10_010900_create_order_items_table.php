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
        Schema::create('order_items', function (Blueprint $table) {
            // id
            $table->id();

            // order_idの外部キー
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');

            // product_idの外部キー
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            //注文数量
            $table->integer('quantity');

            //注文時の金額
            $table->integer('unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};