<?php

namespace App\Contracts\Services;

use App\DTOs\Dashboard\GuardarPromocionDatos;
use App\DTOs\Dashboard\PromocionGestionDatos;
use Illuminate\Http\UploadedFile;

interface GestionPromocionesServiceInterface
{
    /**
     * Crea una promoción asociada al usuario autenticado.
     */
    public function crear(
        GuardarPromocionDatos $datos,
        int $idUsuario,
        ?UploadedFile $imagen = null,
    ): PromocionGestionDatos;

    /**
     * Actualiza una promoción existente.
     */
    public function actualizar(
        int $id,
        GuardarPromocionDatos $datos,
        ?UploadedFile $imagen = null,
    ): PromocionGestionDatos;

    /**
     * Elimina una promoción.
     */
    public function eliminar(int $id): void;

    /**
     * Busca una promoción para el formulario de edición.
     */
    public function buscar(int $id): ?PromocionGestionDatos;

    /**
     * Listado de gestión (todas las promociones).
     *
     * @return list<PromocionGestionDatos>
     */
    public function listar(): array;
}
