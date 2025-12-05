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
        Schema::create('sell_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sell_transaction_id')
                ->constrained('sell_transactions')
                ->cascadeOnDelete();

            $table->foreignId('currency_id')->constrained('currencies');

            // Flatten info
            $table->string('currency_code', 5);
            $table->string('currency_name');
            $table->string('currency_flag')->nullable();

            $table->decimal('nominal_foreign', 18, 2);
            $table->decimal('currency_rate', 18, 2);
            $table->decimal('subtotal_idr', 18, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sell_transaction_items');
    }
};
