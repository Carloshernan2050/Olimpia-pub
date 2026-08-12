<?php

namespace App\Contracts\Services;

interface DatabaseInstallerInterface
{
    /**
     * Ensures the configured database exists.
     *
     * @return bool True when the database was created, false when it already existed.
     */
    public function ensureExists(): bool;
}
