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
        Schema::table('shops', function (Blueprint $table) {
            // 既存のlocationカラムを削除
            // locationカラムが存在しない場合のエラーを避けるため、dropColumnIfExists を使用
            if (Schema::hasColumn('shops', 'location')) {
                $table->dropColumn('location');
            }

            // lat (緯度) と lon (経度) カラムを追加
            // decimal型は浮動小数点数を正確に保存するのに適しています
            // description の後に挿入するように指定
            $table->decimal('lat', 10, 7)->nullable()->after('description');
            $table->decimal('lon', 10, 7)->nullable()->after('lat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            // ロールバック時にlatとlonを削除
            if (Schema::hasColumn('shops', 'lat')) {
                $table->dropColumn('lat');
            }
            if (Schema::hasColumn('shops', 'lon')) {
                $table->dropColumn('lon');
            }
            // もし元のlocationカラムを戻す必要があるなら、ここでgeometry/geography型で再追加
            // ただし、今回はPostGISを使わない方針なので、通常は不要
            // $table->geography('location', 'POINT', 4326)->nullable(); // 元のマイグレーションに合わせてください
        });
    }
};