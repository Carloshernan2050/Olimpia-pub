export function cerrarAlPulsarFuera(detalle) {
    const alPulsar = (evento) => {
        if (!detalle.open || detalle.contains(evento.target)) {
            return;
        }

        detalle.open = false;
    };

    const alTecla = (evento) => {
        if (evento.key === 'Escape' && detalle.open) {
            detalle.open = false;
        }
    };

    document.addEventListener('pointerdown', alPulsar);
    document.addEventListener('keydown', alTecla);
}
