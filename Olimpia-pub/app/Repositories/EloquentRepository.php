<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class EloquentRepository
{
    /**
     * Devuelve la clase del modelo Eloquent que usa el repositorio.
     *
     * @return class-string<Model>
     */
    abstract protected function modelClass(): string;

    /**
     * Persiste un registro con los datos recibidos.
     *
     * @param  array<string, mixed>  $data
     */
    protected function createModel(array $data): Model
    {
        return $this->newQuery()->create($data);
    }

    /**
     * Busca el primer registro que coincida con la columna y el valor.
     */
    protected function findFirstBy(string $column, mixed $value): ?Model
    {
        return $this->newQuery()
            ->where($column, $value)
            ->first();
    }

    /**
     * Devuelve todos los registros del modelo.
     *
     * @return Collection<int, Model>
     */
    protected function allModels(): Collection
    {
        return $this->newQuery()->get();
    }

    /**
     * Abre una consulta nueva sobre el modelo del repositorio.
     */
    private function newQuery(): Builder
    {
        $modelClass = $this->modelClass();

        return (new $modelClass())->newQuery();
    }
}
