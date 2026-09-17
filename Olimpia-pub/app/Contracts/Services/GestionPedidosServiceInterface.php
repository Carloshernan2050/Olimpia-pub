<?php

namespace App\Contracts\Services;

use App\DTOs\Dashboard\GuardarPedidoMesaDatos;
use App\DTOs\Dashboard\PedidoMesaDatos;

interface GestionPedidosServiceInterface
{
    /**
     * Abre el pedido activo de una mesa; falla si ya hay uno.
     */
    public function crearActivo(GuardarPedidoMesaDatos $datos): PedidoMesaDatos;

    /**
     * Crea el pedido activo o agrega productos al que ya está en curso.
     */
    public function registrar(GuardarPedidoMesaDatos $datos): PedidoMesaDatos;

    /**
     * Cierra el pedido activo de la mesa y lo deja en el historial.
     */
    public function terminar(int $idMesa, int $idUsuario): void;
}
