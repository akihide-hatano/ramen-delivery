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

            // delivery_address (配送先住所)
            $table->string('delivery_address', 255);
            // delivery_phone (配送先電話番号) - 新しく追加
            $table->string('delivery_phone', 20); // 電話番号なので短めのVARCHARで十分

            // delivery_zone_name (配達エリア名) - 新しく追加
            $table->string('delivery_zone_name')->nullable(); // configから来るのでnullableでも良いでしょう

            // desired_delivery_time_slot (希望配達時間スロット) - 新しく追加
            $table->string('desired_delivery_time_slot')->nullable(); // ASAPや時間帯文字列が入るのでVARCHAR

            // delivery_notes (配送に関するメモ)
            $table->text('delivery_notes')->nullable(); // メモは長文になる可能性があるのでtext型、任意なのでnullable()

            // total_price (商品合計金額) - total_amountから変更、decimal型に
            $table->decimal('total_price', 10, 2); // 金額はdecimal型で正確に

            // delivery_fee (配送料) - 新しく追加
            $table->decimal('delivery_fee', 10, 2);

            // grand_total (最終合計金額) - 新しく追加
            $table->decimal('grand_total', 10, 2);

            // payment_method (支払い方法) - 新しく追加
            $table->string('payment_method', 50); // cash, credit_card などが入る

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