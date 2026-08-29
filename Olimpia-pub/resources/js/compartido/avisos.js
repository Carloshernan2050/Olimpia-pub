const TIEMPO_VISIBLE_MS = 4000;

export function iniciarAvisos(raiz = document) {
    raiz.querySelectorAll('[data-aviso]').forEach((aviso) => {
        if (!(aviso instanceof HTMLElement) || aviso.dataset.avisoIniciado === '1') {
            return;
        }

        aviso.dataset.avisoIniciado = '1';

        const ocultar = () => {
            window.clearTimeout(temporizador);
            aviso.classList.add('is-oculto');
            aviso.setAttribute('hidden', '');
            aviso.setAttribute('aria-hidden', 'true');

            const grupo = aviso.closest('.avisos-flash');

            if (grupo instanceof HTMLElement && grupo.querySelector('[data-aviso]:not(.is-oculto)') === null) {
                grupo.setAttribute('hidden', '');
            }
        };

        const milisegundos = Number(aviso.dataset.avisoMs);
        const espera = Number.isFinite(milisegundos) && milisegundos > 0 ? milisegundos : TIEMPO_VISIBLE_MS;
        const temporizador = window.setTimeout(ocultar, espera);

        aviso.querySelector('[data-cerrar-aviso]')?.addEventListener('click', ocultar);

        aviso.addEventListener('animationend', (evento) => {
            if (evento.target !== aviso) {
                return;
            }

            ocultar();
        });
    });
}
