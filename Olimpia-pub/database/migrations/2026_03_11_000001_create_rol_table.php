<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de roles.
     */
    public function up(): void
    {
        Schema::create('rol', function (Blueprint $table) {
            $table->increments('id_rol');
            $table->string('nombre_rol', 50)->unique();
        });
    }

    /**
     * Elimina la tabla de roles.
     */
    public function down(): void
    {
        Schema::dropIfExists('rol');
    }
};
