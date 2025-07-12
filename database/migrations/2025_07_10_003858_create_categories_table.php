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
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // カテゴリID (BIGINT, PK, AUTO_INCREMENT)
            $table->string('name', 100)->unique(); // カテゴリ名 (ユニーク制約を追加)
            $table->text('description')->nullable(); // カテゴリの説明

            // 親カテゴリID (自己参照リレーションのため)
            // nullを許容することで、最上位の親カテゴリを表現
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories') // 'categories' テーブルを参照
                ->onDelete('cascade'); // 親カテゴリが削除されたら、子カテゴリも削除

            $table->timestamps(); // created_at と updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};