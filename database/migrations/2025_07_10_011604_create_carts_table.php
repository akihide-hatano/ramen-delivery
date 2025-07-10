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
        Schema::create('carts', function (Blueprint $table) {
            // id <BIGSERIAL, NOT NULL, PK>
            $table->id();

            // user_id <BIGINT, NOT NULL, FK → users.id, ON DELETE CASCADE>
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->unique();

            // shop_id <BIGINT, NULLABLE, FK → shops.id, ON DELETE RESTRICT>
            $table->foreignId('shop_id')->nullable()->constrained('shops')->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};