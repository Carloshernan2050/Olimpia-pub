@props(['bloque'])

@php
    use App\Enums\TipoBloqueInicio;
@endphp

@switch($bloque->tipo)
    @case(TipoBloqueInicio::Texto)
        <x-dashboard.bloque-texto :bloque="$bloque" />
        @break
    @case(TipoBloqueInicio::Video)
        <x-dashboard.bloque-video :bloque="$bloque" />
        @break
    @case(TipoBloqueInicio::Imagen)
        <x-dashboard.bloque-imagen :bloque="$bloque" />
        @break
@endswitch
