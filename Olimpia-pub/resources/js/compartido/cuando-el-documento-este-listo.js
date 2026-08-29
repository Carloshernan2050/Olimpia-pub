export function cuandoElDocumentoEsteListo(iniciar) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciar, { once: true });

        return;
    }

    iniciar();
}
