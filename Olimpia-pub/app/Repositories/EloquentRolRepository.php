<?php

namespace App\Repositories;

use App\Contracts\Repositories\RolRepositoryInterface;
use App\Models\Rol;
use Illuminate\Support\Collection;

class EloquentRolRepository extends EloquentRepository implements RolRepositoryInterface
{
    /**
     * Crea un rol con los datos recibidos.
     */
    public function create(array $data): Rol
    {
        /** @var Rol */
        return $this->createModel($data);
    }

    /**
     * Busca un rol por su nombre.
     */
    public function findByNombre(string $nombreRol): ?Rol
    {
        /** @var Rol|null */
        return $this->findFirstBy('nombre_rol', $nombreRol);
    }

    /**
     * Devuelve todos los roles.
     */
    public function all(): Collection
    {
        return $this->allModels();
    }

    /**
     * @return class-string<Rol>
     */
    protected function modelClass(): string
    {
        return Rol::class;
    }
}
