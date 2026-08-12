<?php

namespace App\Exceptions\Autenticacion;

use Exception;

class CorreoYaRegistradoException extends Exception
{
    public function __construct(string $message = 'El correo ya está registrado.')
    {
        parent::__construct($message);
    }
}
