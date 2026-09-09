<?php

namespace App\DTOs\Dashboard;

use App\Enums\EstadoEvento;
use App\Models\Evento;
use Carbon\CarbonInterface;

final readonly class EventoTarjetaDatos
{
    use ConImagenPublica;

    /**
     * Datos visibles de un evento en el catálogo.
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
        $fecha = $evento->fecha;

        return new self(
            (int) $evento->id_evento,
            $evento->nombre,
            $evento->descripcion,
            $fecha instanceof CarbonInterface ? $fecha->toDateString() : (string) $fecha,
            (string) $evento->hora,
            self::estadoValor($evento->estado),
            $evento->url_imagen,
        );
    }

    /**
     * Fecha en formato corto para la tarjeta.
     */
    public function fechaFormateada(): string
    {
        $marca = strtotime($this->fecha);

        return $marca === false ? $this->fecha : date('d/m/Y', $marca);
    }

    /**
     * Hora en HH:MM.
     */
    public function horaFormateada(): string
    {
        return substr($this->hora, 0, 5);
    }

    /**
     * Segunda línea: descripción o la fecha y hora.
     */
    public function detalle(): string
    {
        if (filled($this->descripcion)) {
            return $this->descripcion;
        }

        return $this->fechaFormateada().' · '.$this->horaFormateada();
    }

    /**
     * Normaliza el estado persistido a su valor de catálogo.
     */
    private static function estadoValor(mixed $estado): string
    {
        if ($estado instanceof EstadoEvento) {
            return $estado->value;
        }

        return EstadoEvento::desdeValor(is_string($estado) ? $estado : null)->value;
    }
}
