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
        Schema::create('sell_transactions', function (Blueprint $table) {
              $table->id();
            $table->string('transaction_code')->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->string('customer_name')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_country')->nullable();
            $table->date('customer_birthdate')->nullable();
            $table->decimal('total_amount', 18, 2)->default(0); // Total dalam IDR
            $table->json('additional_amounts')->nullable(); // Menyimpan detail item pembelian dalam format JSON
            $table->decimal('grand_total', 18, 2)->default(0); // Grand total dalam
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sell_transactions');
    }
};
