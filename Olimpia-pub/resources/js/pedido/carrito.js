export function iniciarCarritoPedido(raiz = document) {
    const pagina = raiz.querySelector('[data-pedido-mesa]');
    const barra = raiz.querySelector('[data-pedido-barra]');
    const formulario = raiz.querySelector('[data-pedido-formulario]');

    if (!(pagina instanceof HTMLElement) || !(barra instanceof HTMLElement) || !(formulario instanceof HTMLFormElement)) {
        return;
    }

    const codigo = pagina.dataset.pedidoCodigo ?? 'mesa';
    const clave = `olimpia-pedido-${codigo}`;
    const items = pagina.hasAttribute('data-pedido-exito') ? [] : leer(clave);

    if (pagina.hasAttribute('data-pedido-exito')) {
        guardar(clave, []);
    }

    pagina.querySelectorAll('[data-pedido-agregar]').forEach((boton) => {
        boton.addEventListener('click', () => {
            if (!(boton instanceof HTMLElement)) {
                return;
            }

            agregar(items, boton);
            guardar(clave, items);
            pintar(barra, formulario, items);
        });
    });

    barra.addEventListener('click', (evento) => {
        const destino = evento.target;

        if (!(destino instanceof HTMLElement)) {
            return;
        }

        const quitar = destino.closest('[data-pedido-quitar]');

        if (!(quitar instanceof HTMLElement)) {
            return;
        }

        const id = Number.parseInt(quitar.dataset.pedidoQuitar ?? '', 10);
        const indice = items.findIndex((item) => item.id === id);

        if (indice < 0) {
            return;
        }

        items.splice(indice, 1);
        guardar(clave, items);
        pintar(barra, formulario, items);
    });

    formulario.addEventListener('submit', (evento) => {
        if (items.length === 0) {
            evento.preventDefault();
        }
    });

    pintar(barra, formulario, items);
}

function agregar(items, boton) {
    const id = Number.parseInt(boton.dataset.id ?? '', 10);
    const precio = Number.parseFloat(boton.dataset.precio ?? '0');
    const nombre = boton.dataset.nombre ?? 'Producto';
    const actual = items.find((item) => item.id === id);

    if (actual) {
        actual.cantidad = Math.min(99, actual.cantidad + 1);

        return;
    }

    items.push({
        id,
        nombre,
        precio,
        cantidad: 1,
    });
}

function pintar(barra, formulario, items) {
    const lista = barra.querySelector('[data-pedido-lista]');
    const campos = barra.querySelector('[data-pedido-campos]');
    const cantidadNodo = barra.querySelector('[data-pedido-cantidad]');
    const plural = barra.querySelector('[data-pedido-plural]');
    const totalNodo = barra.querySelector('[data-pedido-total]');
    const enviar = barra.querySelector('.pedido-enviar');
    const unidades = items.reduce((suma, item) => suma + item.cantidad, 0);
    const total = items.reduce((suma, item) => suma + item.precio * item.cantidad, 0);

    barra.hidden = items.length === 0;

    if (cantidadNodo) {
        cantidadNodo.textContent = String(unidades);
    }

    if (plural) {
        plural.textContent = unidades === 1 ? 'producto' : 'productos';
    }

    if (totalNodo) {
        totalNodo.textContent = formatear(total);
    }

    if (enviar instanceof HTMLButtonElement) {
        enviar.disabled = items.length === 0;
    }

    if (lista instanceof HTMLElement) {
        lista.innerHTML = items.map((item) => `
            <li class="pedido-barra-item">
                <span>${escapar(item.cantidad)} × ${escapar(item.nombre)}</span>
                <button type="button" class="pedido-barra-quitar" data-pedido-quitar="${item.id}">Quitar</button>
            </li>
        `).join('');
    }

    if (campos instanceof HTMLElement) {
        campos.innerHTML = items.map((item, indice) => `
            <input type="hidden" name="lineas[${indice}][id_producto]" value="${item.id}">
            <input type="hidden" name="lineas[${indice}][cantidad]" value="${item.cantidad}">
        `).join('');
    }

    if (items.length === 0) {
        formulario.querySelectorAll('input[name^="lineas"]').forEach((campo) => campo.remove());
    }
}

function leer(clave) {
    try {
        const crudo = window.sessionStorage.getItem(clave);
        const datos = crudo === null ? [] : JSON.parse(crudo);

        return Array.isArray(datos) ? datos.filter(esItem) : [];
    } catch {
        return [];
    }
}

function guardar(clave, items) {
    window.sessionStorage.setItem(clave, JSON.stringify(items));
}

function esItem(valor) {
    return valor !== null
        && typeof valor === 'object'
        && Number.isInteger(valor.id)
        && typeof valor.nombre === 'string'
        && Number.isFinite(valor.precio)
        && Number.isInteger(valor.cantidad)
        && valor.cantidad > 0;
}

function formatear(valor) {
    return `$ ${valor.toFixed(2).replace('.', ',')}`;
}

function escapar(valor) {
    return String(valor)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;');
}
