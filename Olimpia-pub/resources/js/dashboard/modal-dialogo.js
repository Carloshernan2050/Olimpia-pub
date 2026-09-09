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

    if (modal.hasAttribute('data-abrir') && ! modal.open) {
        modal.showModal();
    }
}
