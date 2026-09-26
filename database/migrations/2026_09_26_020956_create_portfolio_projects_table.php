<?php

use App\Enums\PortfolioProjectCategory;
use App\Enums\PortfolioProjectStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('tagline');
            $table->string('url')->nullable();
            $table->string('status')->default(PortfolioProjectStatus::Live->value);
            $table->string('category')->default(PortfolioProjectCategory::Personal->value);
            $table->unsignedSmallInteger('started_year');
            $table->unsignedSmallInteger('ended_year')->nullable();
            $table->text('description');
            $table->json('features');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_projects');
    }
};
