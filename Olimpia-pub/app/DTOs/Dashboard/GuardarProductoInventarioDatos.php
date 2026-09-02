<?php

namespace App\DTOs\Dashboard;

final readonly class GuardarProductoInventarioDatos
{
    /**
     * Datos validados para dar de alta un producto.
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
            max(0, (int) $datos['stock']),
            (int) $datos['id_categoria'],
            ($datos['estado'] ?? 'activo') === 'inactivo' ? 'inactivo' : 'activo',
        );
    }

    /**
     * Atributos para persistir el producto.
     *
     * @return array<string, mixed>
     */
    public function paraCrear(): array
    {
        return [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'stock' => $this->stock,
            'estado' => $this->estado,
            'id_categoria' => $this->idCategoria,
        ];
    }
}
