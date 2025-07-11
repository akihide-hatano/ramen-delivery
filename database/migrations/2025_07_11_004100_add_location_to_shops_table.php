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

        // 既存のlatitude/longitudeデータがある場合、locationカラムに変換して挿入
        // このUPDATE文は、PostGISが有効なPostgreSQLで実行されます
        DB::statement("UPDATE shops SET location = ST_SetSRID(ST_MakePoint(longitude, latitude), 4326)::geography WHERE latitude IS NOT NULL AND longitude IS NOT NULL;");

        // 必要であれば、元のlatitudeとlongitudeカラムを削除
        // Schema::table('shops', function (Blueprint $table) {
        //     $table->dropColumn(['latitude', 'longitude']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('location');
            // 必要であれば、downの場合は元のlatitudeとlongitudeカラムを再追加
            // $table->decimal('latitude', 10, 7)->nullable();
            // $table->decimal('longitude', 10, 7)->nullable();
        });
    }
};