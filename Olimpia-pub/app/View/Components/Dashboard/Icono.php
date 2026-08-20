<?php

namespace App\View\Components\Dashboard;

use App\Support\Dashboard\CatalogoIconos;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Icono extends Component
{
    /**
     * Inyecta el catálogo de iconos y el nombre solicitado.
     */
    public function __construct(
        private readonly CatalogoIconos $catalogo,
        public readonly string $nombre,
        public readonly ?string $titulo = null,
    ) {}

    /**
     * Marca SVG lista para pintar.
     */
    public function svg(): string
    {
        return $this->catalogo->render($this->nombre);
    }

    /**
     * Renderiza el componente de icono.
     */
    public function render(): View
    {
        return view('components.dashboard.icono', [
            'svg' => $this->svg(),
        ]);
    }
}
