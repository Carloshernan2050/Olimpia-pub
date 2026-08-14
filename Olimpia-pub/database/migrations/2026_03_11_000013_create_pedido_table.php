<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de pedidos.
     */
    public function up(): void
    {
        Schema::create('pedido', function (Blueprint $table) {
            $table->increments('id_pedido');
            $table->dateTime('fecha');
            $table->string('estado', 20)->default('pendiente');
            $table->decimal('total', 10, 2)->default(0);
            $table->unsignedInteger('id_mesa');

            $table->foreign('id_mesa')
                ->references('id_mesa')
                ->on('mesa')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Elimina la tabla de pedidos.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido');
    }
};
