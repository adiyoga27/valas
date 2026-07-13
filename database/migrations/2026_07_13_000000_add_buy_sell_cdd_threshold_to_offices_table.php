<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->decimal('buy_cdd_threshold', 18, 2)->nullable()->after('cdd_threshold');
            $table->decimal('sell_cdd_threshold', 18, 2)->nullable()->after('buy_cdd_threshold');
        });
    }

    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn(['buy_cdd_threshold', 'sell_cdd_threshold']);
        });
    }
};
