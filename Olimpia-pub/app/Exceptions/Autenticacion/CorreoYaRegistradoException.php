<?php

namespace App\Exceptions\Autenticacion;

use Exception;

class CorreoYaRegistradoException extends Exception
{
    /**
     * Crea la excepción cuando el correo ya pertenece a otro usuario.
     */
    public function __construct(string $message = 'El correo ya está registrado.')
    {
        parent::__construct($message);
    }
}
