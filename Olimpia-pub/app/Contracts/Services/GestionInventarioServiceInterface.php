<?php

namespace App\Contracts\Services;

use App\DTOs\Dashboard\GuardarMovimientoInventarioDatos;
use App\DTOs\Dashboard\GuardarProductoInventarioDatos;
use App\DTOs\Dashboard\MovimientoInventarioGestionDatos;
use App\DTOs\Dashboard\ProductoInventarioDatos;

interface GestionInventarioServiceInterface
{
    /**
     * Registra un movimiento asociado al usuario autenticado.
     */
    public function crear(
        GuardarMovimientoInventarioDatos $datos,
        int $idUsuario,
    ): MovimientoInventarioGestionDatos;

    /**
     * Actualiza un movimiento existente y ajusta el stock.
     */
    public function actualizar(
        int $id,
        GuardarMovimientoInventarioDatos $datos,
    ): MovimientoInventarioGestionDatos;

    /**
     * Elimina un movimiento y revierte su efecto en el stock.
     */
    public function eliminar(int $id): void;

    /**
     * Busca un movimiento para el formulario de edición.
     */
    public function buscar(int $id): ?MovimientoInventarioGestionDatos;

    /**
     * Crea un producto en el inventario.
     */
    public function crearProducto(
        GuardarProductoInventarioDatos $datos,
        int $idUsuario,
    ): ProductoInventarioDatos;

    /**
     * Busca un producto para la vista de detalle.
     */
    public function buscarProducto(int $id): ?ProductoInventarioDatos;

    /**
     * Elimina un producto del inventario si no tiene pedidos.
     */
    public function eliminarProducto(int $id): void;

    /**
     * Listado de gestión (movimientos recientes).
     *
     * @return list<MovimientoInventarioGestionDatos>
     */
    public function listar(): array;

    /**
     * Movimientos de un producto para la vista de detalle.
     *
     * @return list<MovimientoInventarioGestionDatos>
     */
    public function listarDeProducto(int $idProducto): array;
}
