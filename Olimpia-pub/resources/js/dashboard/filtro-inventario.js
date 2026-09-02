export function iniciarFiltroInventario(raiz = document) {
    raiz.querySelectorAll('[data-filtro-inventario]').forEach((formulario) => {
        if (!(formulario instanceof HTMLFormElement)) {
            return;
        }

        formulario.addEventListener('submit', (evento) => {
            evento.preventDefault();
            aplicarFiltro(formulario);
        });

        formulario.querySelectorAll('select').forEach((select) => {
            select.addEventListener('change', () => formulario.requestSubmit());
        });

        const busqueda = formulario.querySelector('input[type="search"]');

        if (busqueda instanceof HTMLInputElement) {
            busqueda.addEventListener('change', () => formulario.requestSubmit());
        }
    });
}

function aplicarFiltro(formulario) {
    if (formulario.dataset.enviando === '1') {
        return;
    }

    const destino = new URL(formulario.action);

    new FormData(formulario).forEach((valor, nombre) => {
        const texto = String(valor).trim();

        if (texto !== '') {
            destino.searchParams.set(nombre, texto);
        }
    });

    if (destino.href === window.location.href) {
        return;
    }

    formulario.dataset.enviando = '1';
    window.location.assign(destino.href);
}
