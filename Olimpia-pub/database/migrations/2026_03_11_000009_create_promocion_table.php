<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promocion', function (Blueprint $table) {
            $table->increments('id_promocion');
            $table->string('nombre', 150);
            $table->string('descripcion', 255)->nullable();
            $table->decimal('descuento', 5, 2);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('estado', 20)->default('activa');
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
        Schema::dropIfExists('promocion');
    }
};
