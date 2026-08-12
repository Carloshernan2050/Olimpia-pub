<?php

namespace App\Contracts\Repositories;

use App\Models\Usuario;
use Illuminate\Support\Collection;

interface UsuarioRepositoryInterface
{
    public function create(array $data): Usuario;

    public function findByCorreo(string $correo): ?Usuario;

    public function all(): Collection;
}
