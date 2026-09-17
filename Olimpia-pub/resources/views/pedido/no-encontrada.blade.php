@extends('layouts.pedido')

@section('titulo', 'Mesa no encontrada — '.config('app.name', 'Olimpia'))

@section('contenido')
    <section class="pedido-no-encontrada" aria-labelledby="titulo-mesa-ausente">
        <h1 id="titulo-mesa-ausente">Mesa no encontrada</h1>
        <p>{{ $mensaje }}</p>
    </section>
@endsection
