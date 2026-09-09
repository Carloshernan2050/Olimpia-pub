import { iniciarModalDialogo } from './modal-dialogo';

export function iniciarModalPromocion(raiz = document) {
    iniciarModalDialogo(
        raiz,
        '[data-modal-promocion]',
        '[data-abrir-modal-promocion]',
        '[data-cerrar-modal-promocion]',
    );
}
