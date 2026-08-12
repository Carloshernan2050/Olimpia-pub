<?php

namespace App\Exceptions\Autenticacion;

use Exception;

class UsuarioInactivoException extends Exception
{
    public function __construct(string $message = 'La cuenta de usuario no está activa.')
    {
        parent::__construct($message);
    }
}
