<?php

namespace App\Contracts\Repositories;

use App\Models\Historial;

interface HistorialRepositoryInterface
{
    /**
     * Registra una acción en el historial.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Historial;
}
