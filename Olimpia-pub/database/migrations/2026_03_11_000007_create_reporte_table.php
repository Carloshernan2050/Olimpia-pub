<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de reportes.
     */
    public function up(): void
    {
        Schema::create('reporte', function (Blueprint $table) {
            $table->increments('id_reporte');
            $table->string('tipo_reporte', 100);
            $table->dateTime('fecha_generacion');
            $table->string('archivo_pdf');
            $table->unsignedInteger('id_usuario');

            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('usuario')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Elimina la tabla de reportes.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte');
    }
};
