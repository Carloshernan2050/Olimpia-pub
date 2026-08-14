<?php

namespace App\Repositories;

use App\Contracts\Repositories\UsuarioRepositoryInterface;
use App\Models\Usuario;
use Illuminate\Support\Collection;

class EloquentUsuarioRepository implements UsuarioRepositoryInterface
{
    /**
     * Inyecta el modelo de usuario.
     */
    public function __construct(
        private readonly Usuario $model
    ) {
    }

    /**
     * Crea un usuario con los datos recibidos.
     */
    public function create(array $data): Usuario
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * Busca un usuario por su correo electrónico.
     */
    public function findByCorreo(string $correo): ?Usuario
    {
        return $this->model->newQuery()
            ->where('correo', $correo)
            ->first();
    }

    /**
     * Devuelve todos los usuarios.
     */
    public function all(): Collection
    {
        return $this->model->newQuery()->get();
    }
}
