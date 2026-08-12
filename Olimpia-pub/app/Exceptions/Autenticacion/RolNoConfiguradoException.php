<?php

namespace App\Exceptions\Autenticacion;

use Exception;

class RolNoConfiguradoException extends Exception
{
    public function __construct(string $message = 'El rol requerido no está configurado.')
    {
        parent::__construct($message);
    }
}
