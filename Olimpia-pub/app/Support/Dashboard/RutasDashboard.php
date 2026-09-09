<?php

namespace App\Support\Dashboard;

final class RutasDashboard
{
    /**
     * Ruta de un evento identificado por su id.
     */
    public const EVENTO_POR_ID = '/dashboard/eventos/{evento}';

    /**
     * Devuelve la ruta de un evento por id.
     */
    public static function eventoPorId(): string
    {
        return self::EVENTO_POR_ID;
    }
}
