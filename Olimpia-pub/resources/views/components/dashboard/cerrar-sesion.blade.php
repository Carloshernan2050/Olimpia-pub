<form method="POST" action="{{ route('cerrar-sesion') }}">
    @csrf
    <button class="boton-principal" type="submit">Cerrar sesión</button>
</form>
