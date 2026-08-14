<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de códigos QR.
     */
    public function up(): void
    {
        Schema::create('codigo_qr', function (Blueprint $table) {
            $table->increments('id_qr');
            $table->integer('numero_qr')->unique();
            $table->string('estado', 20)->default('activo');
            $table->string('codigo_qr')->unique();
        });
    }

    /**
     * Elimina la tabla de códigos QR.
     */
    public function down(): void
    {
        Schema::dropIfExists('codigo_qr');
    }
};
