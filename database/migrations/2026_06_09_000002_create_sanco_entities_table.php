<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sanco_entities', function (Blueprint $table) {
            $table->id();
            $table->text('entity_id');
            $table->string('dataset_name')->index();
            $table->string('schema')->nullable();
            $table->text('name');
            $table->text('aliases')->nullable();
            $table->text('weak_aliases')->nullable();
            $table->text('countries')->nullable();
            $table->text('birth_date')->nullable();
            $table->text('addresses')->nullable();
            $table->text('identifiers')->nullable();
            $table->text('emails')->nullable();
            $table->string('first_seen')->nullable();
            $table->string('last_seen')->nullable();
            $table->string('last_change')->nullable();
            $table->timestamps();
        });

        Schema::table('sanco_entities', function (Blueprint $table) {
            $table->index('entity_id');
            $table->fullText(['name', 'aliases'], 'sanco_entities_name_aliases_fulltext');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sanco_entities');
    }
};
