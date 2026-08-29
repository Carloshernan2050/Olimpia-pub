@props([
    'nombre' => 'cantidad',
    'valor' => 1,
    'minimo' => 1,
    'maximo' => 99,
])

<div class="selector-cantidad" data-selector-cantidad>
    <button type="button" data-cantidad-menos aria-label="Disminuir cantidad">−</button>
    <input
        type="number"
        name="{{ $nombre }}"
        value="{{ $valor }}"
        min="{{ $minimo }}"
        max="{{ $maximo }}"
        inputmode="numeric"
        aria-label="Cantidad"
        data-cantidad-valor
    >
    <button type="button" data-cantidad-mas aria-label="Aumentar cantidad">+</button>
</div>
