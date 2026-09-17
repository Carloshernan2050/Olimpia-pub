<?php

namespace App\Contracts\Services;

use App\DTOs\Dashboard\GuardarMesaDatos;
use App\DTOs\Dashboard\MesaTarjetaDatos;

interface GestionMesasServiceInterface
{
    /**
     * Crea una mesa con su código QR.
     */
    public function crear(GuardarMesaDatos $datos): MesaTarjetaDatos;

    /**
     * Actualiza el número y el tipo de una mesa.
     */
    public function actualizar(int $id, GuardarMesaDatos $datos): MesaTarjetaDatos;

    /**
     * Elimina una mesa si no tiene pedidos.
     */
    public function eliminar(int $id): void;

    /**
     * Busca una mesa del catálogo, con QR y pedido activo.
     */
    public function buscar(int $id): ?MesaTarjetaDatos;
}
