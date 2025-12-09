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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5); // USD, JPY
            $table->string('name');
            $table->string('flag')->nullable(); // /flags/us.png
            $table->decimal('buy_rate', 18, 2)->default(0.0);   // Customer jual
            $table->decimal('sell_rate', 18, 2)->default(0.0);  // Customer beli
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
