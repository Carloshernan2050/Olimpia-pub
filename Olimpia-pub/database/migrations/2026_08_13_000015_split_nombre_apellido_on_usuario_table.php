<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Separa nombre y apellido en primer y segundo nombre/apellido.
     */
    public function up(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->renameColumn('nombre', 'primer_nombre');
            $table->renameColumn('apellido', 'primer_apellido');
        });

        Schema::table('usuario', function (Blueprint $table) {
            $table->string('segundo_nombre', 100)->nullable()->after('primer_nombre');
            $table->string('segundo_apellido', 100)->nullable()->after('primer_apellido');
        });
    }

    /**
     * Revierte la separación de nombre y apellido.
     */
    public function down(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropColumn(['segundo_nombre', 'segundo_apellido']);
        });

        Schema::table('usuario', function (Blueprint $table) {
            $table->renameColumn('primer_nombre', 'nombre');
            $table->renameColumn('primer_apellido', 'apellido');
        });
    }
};
