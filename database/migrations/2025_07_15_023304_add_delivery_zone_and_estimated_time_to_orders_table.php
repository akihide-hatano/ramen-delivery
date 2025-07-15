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
        Schema::table('orders', function (Blueprint $table) {
            // 配達エリア名を追加
            $table->string('delivery_zone_name', 255)->nullable()->after('delivery_address');
            // 予測配達時間（分）を追加
            $table->integer('estimated_delivery_time_minutes')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery_zone_name');
            $table->dropColumn('estimated_delivery_time_minutes');
        });
    }
};