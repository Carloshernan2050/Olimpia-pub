<?php

namespace App\Contracts\Services;

use App\DTOs\Dashboard\EventoGestionDatos;
use App\DTOs\Dashboard\GuardarEventoDatos;
use Illuminate\Http\UploadedFile;

interface GestionEventosServiceInterface
{
    /**
     * Crea un evento asociado al usuario autenticado.
     */
    public function crear(
        GuardarEventoDatos $datos,
        int $idUsuario,
        ?UploadedFile $imagen = null,
    ): EventoGestionDatos;

    /**
     * Actualiza un evento existente.
     */
    public function actualizar(
        int $id,
        GuardarEventoDatos $datos,
        ?UploadedFile $imagen = null,
    ): EventoGestionDatos;

    /**
     * Elimina un evento.
     */
    public function eliminar(int $id): void;

    /**
     * Busca un evento para el formulario de edición.
     */
    public function buscar(int $id): ?EventoGestionDatos;

    /**
     * Listado de gestión (todos los eventos).
     *
     * @return list<EventoGestionDatos>
     */
    public function listar(): array;
}
