<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sanco_entities', function (Blueprint $table) {
            $table->text('birth_place')->nullable()->after('emails');
            $table->string('gender')->nullable()->after('birth_place');
            $table->text('nationality')->nullable()->after('gender');
            $table->text('position')->nullable()->after('nationality');
            $table->text('notes')->nullable()->after('position');
            $table->json('properties')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('sanco_entities', function (Blueprint $table) {
            $table->dropColumn(['birth_place', 'gender', 'nationality', 'position', 'notes', 'properties']);
        });
    }
};
