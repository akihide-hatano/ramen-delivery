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
            // ユーザーが希望する配達時間（タイムスタンプ）を追加
            // nullを許容し、「できるだけ早く」などの選択肢に対応
            $table->timestamp('desired_delivery_time')->nullable()->after('delivery_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('desired_delivery_time');
        });
    }
};