<?php

namespace App\Exceptions\Autenticacion;

use Exception;

class CredencialesInvalidasException extends Exception
{
    public function __construct(string $message = 'Correo o contraseña incorrectos.')
    {
        parent::__construct($message);
    }
}
