<?php

namespace App\DTOs\Dashboard;

use App\Enums\EstadoEvento;
use App\Models\Evento;

final readonly class EventoDetalleDatos
{
    /**
     * Ficha de detalle: la tarjeta del evento más la etiqueta de estado.
     */
    public function __construct(
        public EventoTarjetaDatos $tarjeta,
        public string $estadoEtiqueta,
    ) {}

    /**
     * Construye el DTO a partir del modelo persistido.
     */
    public static function fromModel(Evento $evento): self
    {
        $tarjeta = EventoTarjetaDatos::fromModel($evento);

        return new self(
            $tarjeta,
            EstadoEvento::desdeValor($tarjeta->estado)->etiqueta(),
        );
    }
}
