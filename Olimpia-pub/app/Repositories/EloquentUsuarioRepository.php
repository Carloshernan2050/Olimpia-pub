<?php

namespace App\Repositories;

use App\Contracts\Repositories\UsuarioRepositoryInterface;
use App\Models\Usuario;
use Illuminate\Support\Collection;

class EloquentUsuarioRepository implements UsuarioRepositoryInterface
{
    public function __construct(
        private readonly Usuario $model
    ) {
    }

    public function create(array $data): Usuario
    {
        return $this->model->newQuery()->create($data);
    }

    public function findByCorreo(string $correo): ?Usuario
    {
        return $this->model->newQuery()
            ->where('correo', $correo)
            ->first();
    }

    public function all(): Collection
    {
        return $this->model->newQuery()->get();
    }
}
