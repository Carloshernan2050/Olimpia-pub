<?php

namespace App\Repositories;

use App\Contracts\Repositories\ContenidoInicioRepositoryInterface;
use App\Models\ContenidoInicio;
use Illuminate\Support\Collection;

class EloquentContenidoInicioRepository extends EloquentRepository implements ContenidoInicioRepositoryInterface
{
    /**
     * Crea un bloque de Home con los datos recibidos.
     */
    public function create(array $data): ContenidoInicio
    {
        /** @var ContenidoInicio */
        return $this->createModel($data);
    }

    /**
     * Busca un bloque por su posición en la grilla.
     */
    public function findByPosicion(string $posicion): ?ContenidoInicio
    {
        /** @var ContenidoInicio|null */
        return $this->findFirstBy('posicion', $posicion);
    }

    /**
     * Devuelve los bloques activos indexados por posición.
     *
     * @return Collection<string, ContenidoInicio>
     */
    public function activosPorPosicion(): Collection
    {
        return $this->newQuery()
            ->where('estado', 'activo')
            ->orderBy('orden')
            ->get()
            ->keyBy(fn (ContenidoInicio $contenido): string => $contenido->posicion->value);
    }

    /**
     * @return class-string<ContenidoInicio>
     */
    protected function modelClass(): string
    {
        return ContenidoInicio::class;
    }
}
