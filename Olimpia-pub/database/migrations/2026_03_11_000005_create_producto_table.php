<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto', function (Blueprint $table) {
            $table->increments('id_producto');
            $table->string('nombre', 150);
            $table->string('descripcion', 255)->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('stock')->default(0);
            $table->string('estado', 20)->default('activo');
            $table->unsignedInteger('id_categoria');

            $table->foreign('id_categoria')
                ->references('id_categoria')
                ->on('categoria')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto');
    }
};
