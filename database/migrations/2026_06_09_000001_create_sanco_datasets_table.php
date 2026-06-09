<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sanco_datasets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->text('url')->nullable();
            $table->string('updated_at_source')->nullable();
            $table->string('last_export')->nullable();
            $table->integer('entity_count')->default(0);
            $table->integer('thing_count')->default(0);
            $table->string('version')->nullable();
            $table->json('tags')->nullable();
            $table->text('publisher_name')->nullable();
            $table->text('publisher_url')->nullable();
            $table->string('publisher_acronym')->nullable();
            $table->string('publisher_country')->nullable();
            $table->string('publisher_country_label')->nullable();
            $table->boolean('publisher_official')->default(false);
            $table->text('publisher_description')->nullable();
            $table->string('coverage_start')->nullable();
            $table->string('coverage_frequency')->nullable();
            $table->json('resources')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sanco_datasets');
    }
};
