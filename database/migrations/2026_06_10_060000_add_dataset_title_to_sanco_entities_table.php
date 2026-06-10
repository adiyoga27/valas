<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sanco_entities', function (Blueprint $table) {
            $table->string('dataset_title')->nullable()->after('dataset_name');
        });
    }

    public function down(): void
    {
        Schema::table('sanco_entities', function (Blueprint $table) {
            $table->dropColumn('dataset_title');
        });
    }
};
