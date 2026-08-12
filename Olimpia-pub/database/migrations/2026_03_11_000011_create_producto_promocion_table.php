<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_promocion', function (Blueprint $table) {
            $table->unsignedInteger('id_producto');
            $table->unsignedInteger('id_promocion');

            $table->primary(['id_producto', 'id_promocion']);

            $table->foreign('id_producto')
                ->references('id_producto')
                ->on('producto')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('id_promocion')
                ->references('id_promocion')
                ->on('promocion')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_promocion');
    }
};
