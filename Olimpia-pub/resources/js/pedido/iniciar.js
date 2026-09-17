import { iniciarAvisos } from '../compartido/avisos';
import { cuandoElDocumentoEsteListo } from '../compartido/cuando-el-documento-este-listo';
import { cerrarAlPulsarFuera } from '../dashboard/cerrar-al-pulsar-fuera';
import { iniciarCarritoPedido } from './carrito';

export function iniciarPedido() {
    cuandoElDocumentoEsteListo(() => {
        document.querySelectorAll('[data-cerrar-al-pulsar-fuera]').forEach((elemento) => {
            if (elemento instanceof HTMLDetailsElement) {
                cerrarAlPulsarFuera(elemento);
            }
        });

        iniciarCarritoPedido();
        iniciarAvisos();
    });
}
