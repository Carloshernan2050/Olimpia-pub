<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de detalles de pedido.
     */
    public function up(): void
    {
        Schema::create('detalle_pedido', function (Blueprint $table) {
            $table->increments('id_detalle');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->unsignedInteger('id_pedido');
            $table->unsignedInteger('id_producto');

            $table->foreign('id_pedido')
                ->references('id_pedido')
                ->on('pedido')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('id_producto')
                ->references('id_producto')
                ->on('producto')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Elimina la tabla de detalles de pedido.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_pedido');
    }
};
