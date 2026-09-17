<?php

namespace App\Contracts\Services;

use App\DTOs\Dashboard\GrupoMesaDatos;
use App\DTOs\Dashboard\GuardarGrupoMesaDatos;
use App\DTOs\Dashboard\GuardarLiberarGruposDatos;

interface GestionGruposMesaServiceInterface
{
    /**
     * Une mesas de tipo mesa en un grupo.
     */
    public function unir(GuardarGrupoMesaDatos $datos): GrupoMesaDatos;

    /**
     * Cambia las mesas que forman el grupo.
     */
    public function actualizar(int $id, GuardarGrupoMesaDatos $datos): GrupoMesaDatos;

    /**
     * Separa el grupo; las mesas vuelven a listarse solas.
     */
    public function separar(int $id): void;

    /**
     * Libera las uniones seleccionadas.
     */
    public function liberar(GuardarLiberarGruposDatos $datos): void;

    /**
     * Busca un grupo con QR de cada mesa y el pedido activo.
     */
    public function buscar(int $id): ?GrupoMesaDatos;

    /**
     * Cierra el pedido activo del grupo y lo deja en el historial.
     */
    public function terminarPedido(int $id, int $idUsuario): void;
}
