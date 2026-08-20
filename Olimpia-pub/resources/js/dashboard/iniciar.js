import { cerrarAlPulsarFuera } from './cerrar-al-pulsar-fuera';

export function iniciarDashboard() {
    document.addEventListener('DOMContentLoaded', () => {
        const menuPerfil = document.querySelector('[data-menu-perfil]');

        if (!(menuPerfil instanceof HTMLDetailsElement)) {
            return;
        }

        cerrarAlPulsarFuera(menuPerfil);
    });
}
