<?php

namespace App\Contracts\Repositories;

use App\Models\Rol;
use Illuminate\Support\Collection;

interface RolRepositoryInterface
{
    /**
     * Crea un rol con los datos recibidos.
     */
    public function create(array $data): Rol;

    /**
     * Busca un rol por su nombre.
     */
    public function findByNombre(string $nombreRol): ?Rol;

    /**
     * Devuelve todos los roles.
     */
    public function all(): Collection;
}
