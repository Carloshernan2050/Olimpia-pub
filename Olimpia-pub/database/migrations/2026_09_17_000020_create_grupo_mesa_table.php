<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permite unir mesas en un grupo y deja barra o mesa como tipos físicos.
     */
    public function up(): void
    {
        Schema::create('grupo_mesa', function (Blueprint $table) {
            $table->increments('id_grupo');
            $table->string('estado', 20)->default('activo');
        });

        Schema::table('mesa', function (Blueprint $table) {
            $table->unsignedInteger('id_grupo')->nullable()->after('tipo');

            $table->foreign('id_grupo')
                ->references('id_grupo')
                ->on('grupo_mesa')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });

        DB::table('mesa')
            ->whereIn('tipo', ['pareja', 'grupo'])
            ->update(['tipo' => 'mesa']);
    }

    /**
     * Quita los grupos y el vínculo en la mesa.
     */
    public function down(): void
    {
        Schema::table('mesa', function (Blueprint $table) {
            $table->dropForeign(['id_grupo']);
            $table->dropColumn('id_grupo');
        });

        Schema::dropIfExists('grupo_mesa');
    }
};
