<?php

namespace App\Contracts\Repositories;

use App\Models\Rol;
use Illuminate\Support\Collection;

interface RolRepositoryInterface
{
    public function create(array $data): Rol;

    public function findByNombre(string $nombreRol): ?Rol;

    public function all(): Collection;
}
