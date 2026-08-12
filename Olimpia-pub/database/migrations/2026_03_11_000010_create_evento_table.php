<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evento', function (Blueprint $table) {
            $table->increments('id_evento');
            $table->string('nombre', 150);
            $table->string('descripcion', 255)->nullable();
            $table->date('fecha');
            $table->string('hora', 10);
            $table->string('estado', 20)->default('programado');
            $table->unsignedInteger('id_usuario');

            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('usuario')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evento');
    }
};
