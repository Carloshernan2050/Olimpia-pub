<?php

namespace App\Contracts\Services;

use App\DTOs\Autenticacion\RegistrarUsuarioDatos;
use App\DTOs\Autenticacion\UsuarioAutenticadoDatos;

interface AutenticacionServiceInterface
{
    /**
     * Registra un usuario, inicia su sesión y devuelve sus datos.
     */
    public function registrar(RegistrarUsuarioDatos $datos): UsuarioAutenticadoDatos;

    /**
     * Autentica al usuario con correo y contraseña.
     */
    public function iniciarSesion(string $correo, string $contrasena): UsuarioAutenticadoDatos;

    /**
     * Cierra la sesión del usuario autenticado.
     */
    public function cerrarSesion(): void;
}
