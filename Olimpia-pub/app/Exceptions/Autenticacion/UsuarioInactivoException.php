<?php

namespace App\Exceptions\Autenticacion;

use Exception;

class UsuarioInactivoException extends Exception
{
    /**
     * Crea la excepción cuando la cuenta del usuario no está activa.
     */
    public function __construct(string $message = 'La cuenta de usuario no está activa.')
    {
        parent::__construct($message);
    }
}
