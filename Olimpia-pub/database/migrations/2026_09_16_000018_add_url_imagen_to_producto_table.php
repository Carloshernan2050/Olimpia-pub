<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega la ruta de la imagen del producto.
     */
    public function up(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->string('url_imagen', 255)->nullable()->after('descripcion');
        });
    }

    /**
     * Quita la ruta de la imagen del producto.
     */
    public function down(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->dropColumn('url_imagen');
        });
    }
};
