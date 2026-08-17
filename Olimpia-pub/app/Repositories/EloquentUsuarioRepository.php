<?php

namespace App\Repositories;

use App\Contracts\Repositories\UsuarioRepositoryInterface;
use App\Models\Usuario;
use Illuminate\Support\Collection;

class EloquentUsuarioRepository extends EloquentRepository implements UsuarioRepositoryInterface
{
    /**
     * Crea un usuario con los datos recibidos.
     */
    public function create(array $data): Usuario
    {
        /** @var Usuario */
        return $this->createModel($data);
    }

    /**
     * Busca un usuario por su correo electrónico.
     */
    public function findByCorreo(string $correo): ?Usuario
    {
        /** @var Usuario|null */
        return $this->findFirstBy('correo', $correo);
    }

    /**
     * Devuelve todos los usuarios.
     */
    public function all(): Collection
    {
        return $this->allModels();
    }

    /**
     * @return class-string<Usuario>
     */
    protected function modelClass(): string
    {
        return Usuario::class;
    }
}
