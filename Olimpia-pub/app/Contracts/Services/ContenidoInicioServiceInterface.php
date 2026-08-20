<?php

namespace App\Contracts\Services;

use App\DTOs\Dashboard\PortadaInicioDatos;

interface ContenidoInicioServiceInterface
{
    /**
     * Obtiene los bloques de la portada de Home.
     */
    public function obtenerPortada(): PortadaInicioDatos;
}
