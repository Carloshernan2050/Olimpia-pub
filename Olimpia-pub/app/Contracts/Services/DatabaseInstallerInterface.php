<?php

namespace App\Contracts\Services;

interface DatabaseInstallerInterface
{
    /**
     * Garantiza que la base de datos configurada exista.
     *
     * @return bool Verdadero si se creó la base de datos, falso si ya existía.
     */
    public function ensureExists(): bool;
}
