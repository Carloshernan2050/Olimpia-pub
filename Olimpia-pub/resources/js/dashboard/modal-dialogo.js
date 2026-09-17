const PARAMETROS_DE_MODAL = ['nueva', 'editar', 'ver', 'editar_producto', 'producto', 'pedido', 'unir', 'liberar', 'ver_grupo', 'editar_grupo', 'pedido_grupo'];

function quitarParametrosDeModal() {
    const url = new URL(window.location.href);
    let huboCambio = false;

    PARAMETROS_DE_MODAL.forEach((parametro) => {
        if (! url.searchParams.has(parametro)) {
            return;
        }

        url.searchParams.delete(parametro);
        huboCambio = true;
    });

    if (huboCambio) {
        window.history.replaceState({}, '', url);
    }
}

export function iniciarModalDialogo(raiz, selectorModal, selectorAbrir, selectorCerrar) {
    const modal = raiz.querySelector(selectorModal);

    if (!(modal instanceof HTMLDialogElement)) {
        return;
    }

    raiz.querySelectorAll(selectorAbrir).forEach((boton) => {
        boton.addEventListener('click', () => modal.showModal());
    });

    modal.querySelectorAll(selectorCerrar).forEach((boton) => {
        boton.addEventListener('click', (evento) => {
            if (boton instanceof HTMLAnchorElement) {
                evento.preventDefault();
            }

            modal.close();
        });
    });

    modal.addEventListener('click', (evento) => {
        if (evento.target === modal) {
            modal.close();
        }
    });

    modal.addEventListener('close', quitarParametrosDeModal);

    if (modal.hasAttribute('data-abrir') && ! modal.open) {
        modal.showModal();
    }
}
