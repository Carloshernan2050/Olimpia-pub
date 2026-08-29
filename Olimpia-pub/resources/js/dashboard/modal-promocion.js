export function iniciarModalPromocion(raiz = document) {
    const modal = raiz.querySelector('[data-modal-promocion]');

    if (!(modal instanceof HTMLDialogElement)) {
        return;
    }

    raiz.querySelectorAll('[data-abrir-modal-promocion]').forEach((boton) => {
        boton.addEventListener('click', () => modal.showModal());
    });

    modal.querySelectorAll('[data-cerrar-modal-promocion]').forEach((boton) => {
        boton.addEventListener('click', () => modal.close());
    });

    modal.addEventListener('click', (evento) => {
        if (evento.target === modal) {
            modal.close();
        }
    });

    if (modal.hasAttribute('data-abrir') && ! modal.open) {
        modal.showModal();
    }
}
