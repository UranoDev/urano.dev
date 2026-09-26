<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolio_projects', function (Blueprint $table) {
            // Ambos opcionales — no todo proyecto tiene marca propia. Si existen,
            // se muestran en la tarjeta y en la página del proyecto; si no, esos
            // espacios simplemente no se dibujan.
            $table->string('logo_path')->nullable()->after('url');
            $table->string('favicon_path')->nullable()->after('logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_projects', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'favicon_path']);
        });
    }
};
