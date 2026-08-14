<?php

namespace App\Contracts\Repositories;

use App\Models\Usuario;
use Illuminate\Support\Collection;

interface UsuarioRepositoryInterface
{
    /**
     * Crea un usuario con los datos recibidos.
     */
    public function create(array $data): Usuario;

    /**
     * Busca un usuario por su correo electrónico.
     */
    public function findByCorreo(string $correo): ?Usuario;

    /**
     * Devuelve todos los usuarios.
     */
    public function all(): Collection;
}
