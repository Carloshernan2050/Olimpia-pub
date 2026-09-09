import { iniciarModalDialogo } from './modal-dialogo';

export function iniciarModalEvento(raiz = document) {
    iniciarModalDialogo(
        raiz,
        '[data-modal-evento]',
        '[data-abrir-modal-evento]',
        '[data-cerrar-modal-evento]',
    );
    iniciarModalDialogo(
        raiz,
        '[data-modal-evento-detalle]',
        '[data-abrir-modal-evento-detalle]',
        '[data-cerrar-modal-evento-detalle]',
    );
}
