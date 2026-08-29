import { iniciarAvisos } from '../compartido/avisos';
import { cuandoElDocumentoEsteListo } from '../compartido/cuando-el-documento-este-listo';
import { cerrarAlPulsarFuera } from './cerrar-al-pulsar-fuera';
import { iniciarModalPromocion } from './modal-promocion';
import { iniciarSelectoresCantidad } from './selector-cantidad';

export function iniciarDashboard() {
    cuandoElDocumentoEsteListo(() => {
        document.querySelectorAll('[data-cerrar-al-pulsar-fuera]').forEach((elemento) => {
            if (elemento instanceof HTMLDetailsElement) {
                cerrarAlPulsarFuera(elemento);
            }
        });

        iniciarSelectoresCantidad();
        iniciarModalPromocion();
        iniciarAvisos();
    });
}
