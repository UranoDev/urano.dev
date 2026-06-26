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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->index();
            $table->string('title');
            $table->string('category');
            $table->string('meta_title');
            $table->string('hero_title');
            $table->text('hero_desc');
            $table->string('cta_text')->default('Solicitar Demo Gratis');
            $table->string('benefits_title');
            $table->string('benefits_subtitle');
            $table->json('benefits'); // Storing benefits as JSON
            $table->text('quote')->nullable();
            $table->string('quote_author')->nullable();
            $table->string('modules_title');
            $table->json('modules'); // Storing modules as JSON
            $table->string('cta_title');
            $table->text('cta_desc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
