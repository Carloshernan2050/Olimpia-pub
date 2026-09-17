<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Distingue las mesas de barra, pareja y grupo.
     */
    public function up(): void
    {
        Schema::table('mesa', function (Blueprint $table) {
            $table->string('tipo', 20)->default('pareja')->after('numero_mesa');
        });
    }

    /**
     * Quita el tipo de la mesa.
     */
    public function down(): void
    {
        Schema::table('mesa', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
