<?php

namespace App\Repositories;

use App\Contracts\Repositories\RolRepositoryInterface;
use App\Models\Rol;
use Illuminate\Support\Collection;

class EloquentRolRepository implements RolRepositoryInterface
{
    /**
     * Inyecta el modelo de rol.
     */
    public function __construct(
        private readonly Rol $model
    ) {
    }

    /**
     * Crea un rol con los datos recibidos.
     */
    public function create(array $data): Rol
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * Busca un rol por su nombre.
     */
    public function findByNombre(string $nombreRol): ?Rol
    {
        return $this->model->newQuery()
            ->where('nombre_rol', $nombreRol)
            ->first();
    }

    /**
     * Devuelve todos los roles.
     */
    public function all(): Collection
    {
        return $this->model->newQuery()->get();
    }
}
