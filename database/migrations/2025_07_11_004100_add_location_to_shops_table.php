<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // DBファサードをuseする

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            // geography型のカラムを追加 (POINTは点のジオメトリ、4326はWGS84座標系)
            // PostGISのST_MakePointは経度、緯度の順なので注意！
            $table->geography('location', 'POINT', 4326)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }
};