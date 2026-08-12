<?php

namespace App\Repositories;

use App\Contracts\Repositories\RolRepositoryInterface;
use App\Models\Rol;
use Illuminate\Support\Collection;

class EloquentRolRepository implements RolRepositoryInterface
{
    public function __construct(
        private readonly Rol $model
    ) {
    }

    public function create(array $data): Rol
    {
        return $this->model->newQuery()->create($data);
    }

    public function findByNombre(string $nombreRol): ?Rol
    {
        return $this->model->newQuery()
            ->where('nombre_rol', $nombreRol)
            ->first();
    }

    public function all(): Collection
    {
        return $this->model->newQuery()->get();
    }
}
