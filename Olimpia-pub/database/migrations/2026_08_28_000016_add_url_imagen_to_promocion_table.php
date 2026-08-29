<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega la ruta de la imagen de la promoción.
     */
    public function up(): void
    {
        Schema::table('promocion', function (Blueprint $table) {
            $table->string('url_imagen', 255)->nullable()->after('descripcion');
        });
    }

    /**
     * Quita la ruta de la imagen de la promoción.
     */
    public function down(): void
    {
        Schema::table('promocion', function (Blueprint $table) {
            $table->dropColumn('url_imagen');
        });
    }
};
