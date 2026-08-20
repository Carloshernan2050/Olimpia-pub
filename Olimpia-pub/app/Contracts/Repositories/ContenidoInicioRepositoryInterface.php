<?php

namespace App\Contracts\Repositories;

use App\Models\ContenidoInicio;
use Illuminate\Support\Collection;

interface ContenidoInicioRepositoryInterface
{
    /**
     * Crea un bloque de Home con los datos recibidos.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): ContenidoInicio;

    /**
     * Busca un bloque por su posición en la grilla.
     */
    public function findByPosicion(string $posicion): ?ContenidoInicio;

    /**
     * Devuelve los bloques activos indexados por posición.
     *
     * @return Collection<string, ContenidoInicio>
     */
    public function activosPorPosicion(): Collection;
}
