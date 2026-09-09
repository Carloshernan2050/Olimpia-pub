<?php

namespace App\Contracts\Repositories;

use App\DTOs\Dashboard\FiltroRangoFechasDatos;
use App\Models\Evento;
use Illuminate\Support\Collection;

interface EventoRepositoryInterface
{
    /**
     * Crea un evento con los datos recibidos.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Evento;

    /**
     * Actualiza un evento existente.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Evento $evento, array $data): Evento;

    /**
     * Elimina un evento.
     */
    public function delete(Evento $evento): void;

    /**
     * Busca un evento por su identificador.
     */
    public function findById(int $id): ?Evento;

    /**
     * Todos los eventos para el listado de gestión.
     *
     * @return Collection<int, Evento>
     */
    public function todas(): Collection;

    /**
     * Eventos filtrados y ordenados para el catálogo.
     *
     * @return Collection<int, Evento>
     */
    public function listar(?FiltroRangoFechasDatos $filtro = null): Collection;
}
