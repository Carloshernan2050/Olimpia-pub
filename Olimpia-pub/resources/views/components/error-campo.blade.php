@props(['nombre'])

@error($nombre)
    <div {{ $attributes->class('error') }}>{{ $message }}</div>
@enderror
