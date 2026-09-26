<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolio_projects', function (Blueprint $table) {
            $table->unsignedTinyInteger('started_month')->nullable()->after('started_year');
            $table->unsignedTinyInteger('ended_month')->nullable()->after('ended_year');
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_projects', function (Blueprint $table) {
            $table->dropColumn(['started_month', 'ended_month']);
        });
    }
};
