<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimiento_inventario', function (Blueprint $table) {
            $table->increments('id_movimiento');
            $table->string('tipo_movimiento', 50);
            $table->integer('cantidad');
            $table->dateTime('fecha');
            $table->unsignedInteger('id_producto');
            $table->unsignedInteger('id_usuario');

            $table->foreign('id_producto')
                ->references('id_producto')
                ->on('producto')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('usuario')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimiento_inventario');
    }
};
