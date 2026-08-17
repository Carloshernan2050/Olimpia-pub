<?php

namespace App\Exceptions;

use Exception;

abstract class ExcepcionConContexto extends Exception
{
    protected const PLANTILLA = '%s';

    public function __construct(string $contexto)
    {
        parent::__construct(sprintf(static::PLANTILLA, $contexto));
    }
}
