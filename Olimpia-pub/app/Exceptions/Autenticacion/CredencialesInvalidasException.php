<?php

namespace App\Exceptions\Autenticacion;

use Exception;

class CredencialesInvalidasException extends Exception
{
    /**
     * Crea la excepción cuando el correo o la contraseña no coinciden.
     */
    public function __construct(string $message = 'Correo o contraseña incorrectos.')
    {
        parent::__construct($message);
    }
}
