<?php

namespace App\Enums;

enum TipoMesa: string
{
    case Barra = 'barra';
    case Mesa = 'mesa';
    case Grupo = 'grupo';
    case PedidosActivos = 'pedidos-activos';

    /**
     * Etiqueta visible en el filtro del catálogo.
     */
    public function etiqueta(): string
    {
        return match ($this) {
            self::Barra => 'Barra',
            self::Mesa => 'Mesa',
            self::Grupo => 'Grupo',
            self::PedidosActivos => 'Pedidos activos',
        };
    }

    /**
     * Icono de la barra de tipos.
     */
    public function icono(): string
    {
        return match ($this) {
            self::Barra => 'taburete',
            self::Mesa => 'mesa',
            self::Grupo => 'grupo',
            self::PedidosActivos => 'carrito',
        };
    }

    /**
     * Clase CSS del chip y de la fila.
     */
    public function clase(): string
    {
        return 'is-'.$this->value;
    }

    /**
     * El grupo y los pedidos activos no se persisten en una mesa individual.
     */
    public function esPersistible(): bool
    {
        return $this === self::Barra || $this === self::Mesa;
    }

    /**
     * El filtro de grupo lista las mesas unidas.
     */
    public function esGrupo(): bool
    {
        return $this === self::Grupo;
    }

    /**
     * El filtro lista mesas y uniones con pedido en curso.
     */
    public function esPedidosActivos(): bool
    {
        return $this === self::PedidosActivos;
    }

    /**
     * Interpreta el valor persistido; pareja o grupo antiguos quedan como mesa.
     */
    public static function desdeValor(?string $valor): self
    {
        return match ($valor) {
            self::Barra->value => self::Barra,
            default => self::Mesa,
        };
    }

    /**
     * Tipos que una mesa individual puede guardar.
     *
     * @return list<string>
     */
    public static function valoresPersistibles(): array
    {
        return [self::Barra->value, self::Mesa->value];
    }

    /**
     * @return list<string>
     */
    public static function valores(): array
    {
        return array_column(self::cases(), 'value');
    }
}
