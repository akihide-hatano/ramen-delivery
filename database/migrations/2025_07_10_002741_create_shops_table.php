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
        Schema::create('shops', function (Blueprint $table) {
            // id <BIGSERIAL, NOT NULL, PK>
            $table->id(); // PostgreSQLのBIGSERIALに相当

            //店舗名
            $table->string('name', 100);

            //住所
            $table->string('address', 255);

            //電話番号
            $table->string('phone_number', 20)->nullable();

            //店内写真
            $table->string('photo_1_url', 255)->nullable();
            $table->string('photo_2_url', 255)->nullable();
            $table->string('photo_3_url', 255)->nullable();

            //お店の説明
            $table->text('description')->nullable();

            //駐車場の有無
            $table->boolean('has_parking')->default(false);

            //テーブル席
            $table->boolean('has_table_seats')->default(false);

            //カウンター
            $table->boolean('has_counter_seats')->default(false);

            //営業時間
            $table->string('business_hours', 100)->nullable();

            //休日
            $table->string('regular_holiday', 100)->nullable();

            //現金
            $table->boolean('accept_cash')->default(true);

            //カード支払い
            $table->boolean('accept_credit_card')->default(false);

            //電子マネー
            $table->boolean('accept_e_money')->default(false);

            $table->timestamps(); // created_at と updated_at (TIMESTAMPTZ) を自動生成
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};