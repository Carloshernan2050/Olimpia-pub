<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de bloques de la portada de Home.
     */
    public function up(): void
    {
        Schema::create('contenido_inicio', function (Blueprint $table) {
            $table->increments('id_contenido_inicio');
            $table->string('posicion', 40)->unique();
            $table->string('tipo', 20);
            $table->string('titulo', 150)->nullable();
            $table->text('cuerpo')->nullable();
            $table->string('url_media', 255)->nullable();
            $table->unsignedTinyInteger('orden')->default(1);
            $table->string('estado', 20)->default('activo');
        });
    }

    /**
     * Elimina la tabla de bloques de Home.
     */
    public function down(): void
    {
        Schema::dropIfExists('contenido_inicio');
    }
};
