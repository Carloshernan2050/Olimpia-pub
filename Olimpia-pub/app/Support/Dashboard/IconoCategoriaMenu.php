<?php

namespace App\Support\Dashboard;

final class IconoCategoriaMenu
{
    /**
     * Icono de la carta según el nombre de la categoría de inventario.
     */
    public function para(string $nombre): string
    {
        return match (mb_strtolower(trim($nombre))) {
            'comidas' => 'comida',
            'bebidas' => 'taza',
            'postres' => 'pan',
            default => 'caja',
        };
    }

    /**
     * Peso para ordenar las chips como en la carta: comidas, bebidas, postres.
     */
    public function peso(string $nombre): int
    {
        return match (mb_strtolower(trim($nombre))) {
            'comidas' => 0,
            'bebidas' => 1,
            'postres' => 2,
            default => 100,
        };
    }
}
