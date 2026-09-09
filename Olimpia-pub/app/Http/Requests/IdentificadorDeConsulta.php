<?php

namespace App\Http\Requests;

trait IdentificadorDeConsulta
{
    /**
     * Acepta solo enteros positivos; cualquier otro valor se ignora.
     */
    private function identificadorPositivo(mixed $id): ?int
    {
        if (! is_numeric($id) || (int) $id < 1) {
            return null;
        }

        return (int) $id;
    }
}
