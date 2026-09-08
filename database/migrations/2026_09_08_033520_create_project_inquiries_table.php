<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('company')->nullable();
            $table->text('project_description');
            $table->string('timeframe');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_inquiries');
    }
};
