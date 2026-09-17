import { iniciarModalDialogo } from './modal-dialogo';

export function iniciarModalMesa(raiz = document) {
    iniciarModalDialogo(
        raiz,
        '[data-modal-mesa]',
        '[data-abrir-modal-mesa]',
        '[data-cerrar-modal-mesa]',
    );
}
