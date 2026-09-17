<?php

namespace App\Support\Dashboard;

final class RutasDashboard
{
    /**
     * Ruta de un evento identificado por su id.
     */
    public const EVENTO_POR_ID = '/dashboard/eventos/{evento}';

    /**
     * Ruta de una mesa identificada por su id.
     */
    public const MESA_POR_ID = '/dashboard/mesas/{mesa}';

    /**
     * Ruta de un grupo de mesas identificado por su id.
     */
    public const GRUPO_MESA_POR_ID = '/dashboard/mesas/grupos/{grupo}';

    /**
     * Devuelve la ruta de un evento por id.
     */
    public static function eventoPorId(): string
    {
        return self::EVENTO_POR_ID;
    }

    /**
     * Devuelve la ruta de una mesa por id.
     */
    public static function mesaPorId(): string
    {
        return self::MESA_POR_ID;
    }

    /**
     * Devuelve la ruta de un grupo de mesas por id.
     */
    public static function grupoMesaPorId(): string
    {
        return self::GRUPO_MESA_POR_ID;
    }
}
