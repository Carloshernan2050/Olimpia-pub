<?php

namespace App\Exceptions;

use Exception;
use Throwable;

abstract class ExcepcionDeDominio extends Exception
{
    protected const MENSAJE = 'Ha ocurrido un error.';

    public function __construct(?string $message = null, ?Throwable $previous = null)
    {
        parent::__construct($message ?? static::MENSAJE, 0, $previous);
    }
}
