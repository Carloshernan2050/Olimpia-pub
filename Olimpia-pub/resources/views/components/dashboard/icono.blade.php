<span
    {{ $attributes->class('icono-dashboard') }}
    @if ($titulo)
        role="img"
        aria-label="{{ $titulo }}"
    @else
        aria-hidden="true"
    @endif
>
    {!! $svg !!}
</span>
