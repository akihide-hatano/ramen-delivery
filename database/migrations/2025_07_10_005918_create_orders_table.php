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
        Schema::create('orders', function (Blueprint $table) {
            // id
            $table->id();
            // user_idの外部キー
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // shop_idの外部キー
            $table->foreignId('shop_id')->constrained('shops')->onDelete('cascade');

            // delivery_address
            $table->string('delivery_address', 255);
            $table->text('delivery_notes')->nullable(); // メモは長文になる可能性があるのでtext型、任意なのでnullable()
            // total_amount
            $table->integer('total_amount');

            // status <VARCHAR(20), NOT NULL, DEFAULT 'pending'>
            // CHECK IN ('pending','preparing','delivering','delivered','cancelled')はアプリケーション側でバリデーション
            $table->string('status', 20)->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};