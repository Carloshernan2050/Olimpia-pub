<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de mesas.
     */
    public function up(): void
    {
        Schema::create('mesa', function (Blueprint $table) {
            $table->increments('id_mesa');
            $table->integer('numero_mesa')->unique();
            $table->string('estado', 20)->default('disponible');
            $table->unsignedInteger('id_qr')->unique();

            $table->foreign('id_qr')
                ->references('id_qr')
                ->on('codigo_qr')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Elimina la tabla de mesas.
     */
    public function down(): void
    {
        Schema::dropIfExists('mesa');
    }
};
