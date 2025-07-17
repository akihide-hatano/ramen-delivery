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
        Schema::table('users', function (Blueprint $table) {
            // 住所カラムを追加 (nullableで、後から入力できるように)
            $table->string('address')->nullable()->after('email');
            // 電話番号カラムを追加 (nullableで、後から入力できるように)
            $table->string('phone_number')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ロールバック時にカラムを削除
            $table->dropColumn('address');
            $table->dropColumn('phone_number');
        });
    }
};
