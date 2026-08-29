export function iniciarSelectoresCantidad(raiz = document) {
    raiz.querySelectorAll('[data-selector-cantidad]').forEach((grupo) => {
        const menos = grupo.querySelector('[data-cantidad-menos]');
        const mas = grupo.querySelector('[data-cantidad-mas]');
        const valor = grupo.querySelector('[data-cantidad-valor]');

        if (!(valor instanceof HTMLInputElement)) {
            return;
        }

        menos?.addEventListener('click', () => cambiarCantidad(valor, -1));
        mas?.addEventListener('click', () => cambiarCantidad(valor, 1));
        valor.addEventListener('change', () => normalizarCantidad(valor));
    });
}

function cambiarCantidad(campo, delta) {
    const actual = Number.parseInt(campo.value, 10) || minimoDe(campo);

    campo.value = String(limitar(actual + delta, campo));
}

function normalizarCantidad(campo) {
    campo.value = String(limitar(Number.parseInt(campo.value, 10) || minimoDe(campo), campo));
}

function limitar(valor, campo) {
    const minimo = minimoDe(campo);
    const maximo = Number.parseInt(campo.max, 10) || 99;

    return Math.min(maximo, Math.max(minimo, valor));
}

function minimoDe(campo) {
    const minimo = Number.parseInt(campo.min, 10);

    return Number.isNaN(minimo) ? 1 : minimo;
}
