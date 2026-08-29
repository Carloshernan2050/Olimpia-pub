<?php

namespace App\DTOs\Dashboard;

use App\Models\Promocion;

final readonly class PromocionTarjetaDatos
{
    use ConImagenPublica;

    /**
     * Datos visibles de una promoción en el catálogo.
     */
    public function __construct(
        public int $id,
        public string $nombre,
        public ?string $descripcion,
        public string $descuento,
        public ?string $urlImagen = null,
    ) {}

    /**
     * Construye el DTO a partir del modelo persistido.
     */
    public static function fromModel(Promocion $promocion): self
    {
        return new self(
            (int) $promocion->id_promocion,
            $promocion->nombre,
            $promocion->descripcion,
            (string) $promocion->descuento,
            $promocion->url_imagen,
        );
    }

    /**
     * Segunda línea de la tarjeta: descripción o el descuento.
     */
    public function detalle(): string
    {
        if (filled($this->descripcion)) {
            return $this->descripcion;
        }

        return $this->descuentoFormateado().'% de descuento';
    }

    /**
     * Quita ceros innecesarios del porcentaje.
     */
    private function descuentoFormateado(): string
    {
        return rtrim(rtrim($this->descuento, '0'), '.') ?: '0';
    }
}
