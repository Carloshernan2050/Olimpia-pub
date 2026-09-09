import { iniciarModalDialogo } from './modal-dialogo';

export function iniciarModalInventario(raiz = document) {
    iniciarModalDialogo(
        raiz,
        '[data-modal-inventario]',
        '[data-abrir-modal-inventario]',
        '[data-cerrar-modal-inventario]',
    );
}
