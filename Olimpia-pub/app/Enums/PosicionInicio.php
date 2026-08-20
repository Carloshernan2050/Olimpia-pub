<?php

namespace App\Enums;

enum PosicionInicio: string
{
    case SuperiorIzquierda = 'superior_izquierda';
    case SuperiorCentro = 'superior_centro';
    case SuperiorDerecha = 'superior_derecha';
    case InferiorIzquierda = 'inferior_izquierda';
    case InferiorCentro = 'inferior_centro';
    case InferiorDerecha = 'inferior_derecha';

    /**
     * Recorre las posiciones en el orden de la grilla de Home.
     *
     * @return list<self>
     */
    public static function enOrdenDeGrilla(): array
    {
        return [
            self::SuperiorIzquierda,
            self::SuperiorCentro,
            self::SuperiorDerecha,
            self::InferiorIzquierda,
            self::InferiorCentro,
            self::InferiorDerecha,
        ];
    }

    /**
     * Tipo de bloque que corresponde a esta posición en Home.
     */
    public function tipo(): TipoBloqueInicio
    {
        return match ($this) {
            self::SuperiorIzquierda,
            self::SuperiorDerecha,
            self::InferiorCentro => TipoBloqueInicio::Texto,
            self::SuperiorCentro => TipoBloqueInicio::Video,
            self::InferiorIzquierda,
            self::InferiorDerecha => TipoBloqueInicio::Imagen,
        };
    }
}
