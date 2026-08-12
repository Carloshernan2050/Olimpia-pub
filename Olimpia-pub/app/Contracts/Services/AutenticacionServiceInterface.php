<?php

namespace App\Contracts\Services;

use App\Models\Usuario;

interface AutenticacionServiceInterface
{
    /**
     * @param  array{nombre: string, apellido: string, correo: string, contrasena: string}  $datos
     */
    public function registrar(array $datos): Usuario;

    public function iniciarSesion(string $correo, string $contrasena): Usuario;

    public function cerrarSesion(): void;
}
