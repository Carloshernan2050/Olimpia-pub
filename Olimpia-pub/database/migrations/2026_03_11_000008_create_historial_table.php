<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial', function (Blueprint $table) {
            $table->increments('id_historial');
            $table->string('accion', 100);
            $table->dateTime('fecha');
            $table->string('descripcion', 255)->nullable();
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
        Schema::dropIfExists('historial');
    }
};
