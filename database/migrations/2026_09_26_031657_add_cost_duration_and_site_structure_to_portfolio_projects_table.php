<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolio_projects', function (Blueprint $table) {
            // Nulos en proyectos personales: ahí no aplica un costo de cliente.
            $table->string('cost_label')->nullable()->after('description');
            $table->text('cost_note')->nullable()->after('cost_label');
            $table->string('duration_label')->nullable()->after('cost_note');
            $table->text('duration_note')->nullable()->after('duration_label');
            $table->json('site_structure')->nullable()->after('duration_note');
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_projects', function (Blueprint $table) {
            $table->dropColumn(['cost_label', 'cost_note', 'duration_label', 'duration_note', 'site_structure']);
        });
    }
};
