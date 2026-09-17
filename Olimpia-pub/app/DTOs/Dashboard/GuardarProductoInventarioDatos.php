<?php

namespace App\DTOs\Dashboard;

final readonly class GuardarProductoInventarioDatos
{
    /**
     * Datos validados para crear o actualizar un producto.
     */
    public function __construct(
        public string $nombre,
        public ?string $descripcion,
        public string $precio,
        public int $stock,
        public int $idCategoria,
        public string $estado,
    ) {}

    /**
     * @param  array<string, mixed>  $datos
     */
    public static function fromValidated(array $datos): self
    {
        $descripcion = trim((string) ($datos['descripcion'] ?? ''));

        return new self(
            trim((string) $datos['nombre']),
            $descripcion === '' ? null : $descripcion,
            (string) $datos['precio'],
            max(0, (int) ($datos['stock'] ?? 0)),
            (int) $datos['id_categoria'],
            ($datos['estado'] ?? 'activo') === 'inactivo' ? 'inactivo' : 'activo',
        );
    }

    /**
     * Atributos para persistir un producto nuevo.
     *
     * @return array<string, mixed>
     */
    public function paraCrear(): array
    {
        return [
            ...$this->paraActualizar(),
            'stock' => $this->stock,
        ];
    }

    /**
     * Atributos para actualizar un producto existente, sin alterar el stock.
     *
     * @return array<string, mixed>
     */
    public function paraActualizar(): array
    {
        return [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'estado' => $this->estado,
            'id_categoria' => $this->idCategoria,
        ];
    }
}
