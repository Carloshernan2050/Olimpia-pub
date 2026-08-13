<?php

namespace App\Contracts\Services;

use App\DTOs\Autenticacion\RegistrarUsuarioDatos;
use App\DTOs\Autenticacion\UsuarioAutenticadoDatos;

interface AutenticacionServiceInterface
{
    public function registrar(RegistrarUsuarioDatos $datos): UsuarioAutenticadoDatos;

    public function iniciarSesion(string $correo, string $contrasena): UsuarioAutenticadoDatos;

    public function cerrarSesion(): void;
}
