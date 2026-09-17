<?php

namespace App\DTOs\Dashboard;

use App\Enums\TipoMesa;

final readonly class GuardarMesaDatos
{
    /**
     * Datos validados para crear o actualizar una mesa.
     */
    public function __construct(
        public int $numero,
        public TipoMesa $tipo,
    ) {}

    /**
     * @param  array<string, mixed>  $datos
     */
    public static function fromValidated(array $datos): self
    {
        return new self(
            (int) $datos['numero_mesa'],
            self::tipoPersistible($datos['tipo'] ?? null),
        );
    }

    /**
     * Atributos para persistir una mesa nueva.
     *
     * @return array<string, mixed>
     */
    public function paraCrear(int $idQr): array
    {
        return [
            ...$this->paraActualizar(),
            'estado' => 'disponible',
            'id_qr' => $idQr,
        ];
    }

    /**
     * Atributos para actualizar una mesa existente.
     *
     * @return array<string, mixed>
     */
    public function paraActualizar(): array
    {
        return [
            'numero_mesa' => $this->numero,
            'tipo' => $this->tipo->value,
        ];
    }

    /**
     * Código persistido que luego se convierte en QR.
     */
    public function codigoQr(): string
    {
        return self::codigoDeNumero($this->numero);
    }

    /**
     * Código a partir del número de mesa.
     */
    public static function codigoDeNumero(int $numero): string
    {
        return 'OLIMPIA-MESA-'.str_pad((string) $numero, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Solo barra o mesa se pueden guardar en una mesa individual.
     */
    private static function tipoPersistible(mixed $valor): TipoMesa
    {
        $tipo = is_string($valor) ? TipoMesa::tryFrom($valor) : null;

        if ($tipo === null || ! $tipo->esPersistible()) {
            return TipoMesa::Mesa;
        }

        return $tipo;
    }
}
