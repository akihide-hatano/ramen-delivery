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
        Schema::table('order_items', function (Blueprint $table) {
            // subtotal カラムを追加
            // decimal 型で、合計金額なので10桁、小数点以下2桁
            // nullを許容しない場合は notNullable() を追加
            $table->decimal('subtotal', 10, 2)->after('unit_price')->default(0); // ★ここを追加
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // ロールバック時に subtotal カラムを削除
            $table->dropColumn('subtotal'); // ★ここを追加
        });
    }
};