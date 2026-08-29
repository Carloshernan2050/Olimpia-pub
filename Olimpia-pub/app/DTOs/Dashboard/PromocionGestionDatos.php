<?php

namespace App\DTOs\Dashboard;

use App\Models\Promocion;

final readonly class PromocionGestionDatos
{
    use ConImagenPublica;

    public string $fechaInicio;

    public string $fechaFin;

    /**
     * Datos de una promoción para el formulario y el listado de gestión.
     */
    public function __construct(
        public int $id,
        public string $nombre,
        public ?string $descripcion,
        public string $descuento,
        PeriodoPromocion $periodo,
        public string $estado,
        public ?string $urlImagen = null,
    ) {
        $this->fechaInicio = $periodo->inicio;
        $this->fechaFin = $periodo->fin;
    }

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
            PeriodoPromocion::fromModel($promocion),
            (string) $promocion->estado,
            $promocion->url_imagen,
        );
    }

    /**
     * Indica si la promoción está marcada como activa.
     */
    public function estaActiva(): bool
    {
        return $this->estado === 'activa';
    }
}
