<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de categorías.
     */
    public function up(): void
    {
        Schema::create('categoria', function (Blueprint $table) {
            $table->increments('id_categoria');
            $table->string('nombre', 100)->unique();
            $table->string('descripcion', 255)->nullable();
        });
    }

    /**
     * Elimina la tabla de categorías.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria');
    }
};
