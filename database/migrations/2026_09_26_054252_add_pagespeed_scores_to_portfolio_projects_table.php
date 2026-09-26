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
        Schema::table('portfolio_projects', function (Blueprint $table) {
            $table->unsignedTinyInteger('pagespeed_performance')->nullable();
            $table->unsignedTinyInteger('pagespeed_accessibility')->nullable();
            $table->unsignedTinyInteger('pagespeed_best_practices')->nullable();
            $table->unsignedTinyInteger('pagespeed_seo')->nullable();
            $table->timestamp('pagespeed_measured_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portfolio_projects', function (Blueprint $table) {
            $table->dropColumn([
                'pagespeed_performance',
                'pagespeed_accessibility',
                'pagespeed_best_practices',
                'pagespeed_seo',
                'pagespeed_measured_at',
            ]);
        });
    }
};
