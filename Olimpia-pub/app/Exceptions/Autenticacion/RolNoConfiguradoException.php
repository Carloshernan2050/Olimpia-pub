<?php

namespace App\Exceptions\Autenticacion;

use Exception;

class RolNoConfiguradoException extends Exception
{
    /**
     * Crea la excepción cuando el rol requerido no está configurado.
     */
    public function __construct(string $message = 'El rol requerido no está configurado.')
    {
        parent::__construct($message);
    }
}
