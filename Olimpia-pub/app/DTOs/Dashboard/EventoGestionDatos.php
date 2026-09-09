<?php

namespace App\DTOs\Dashboard;

use App\Models\Evento;

final readonly class EventoGestionDatos
{
    use ConImagenPublica;

    /**
     * Datos de un evento para el formulario y el listado de gestión.
     */
    public function __construct(
        public int $id,
        public string $nombre,
        public ?string $descripcion,
        public string $fecha,
        public string $hora,
        public string $estado,
        public ?string $urlImagen = null,
    ) {}

    /**
     * Construye el DTO a partir del modelo persistido.
     */
    public static function fromModel(Evento $evento): self
    {
        $tarjeta = EventoTarjetaDatos::fromModel($evento);

        return new self(
            $tarjeta->id,
            $tarjeta->nombre,
            $tarjeta->descripcion,
            $tarjeta->fecha,
            $tarjeta->horaFormateada(),
            $tarjeta->estado,
            $tarjeta->urlImagen,
        );
    }

    /**
     * Línea secundaria del listado: fecha y hora.
     */
    public function cuando(): string
    {
        return $this->fecha.' · '.$this->hora;
    }
}
