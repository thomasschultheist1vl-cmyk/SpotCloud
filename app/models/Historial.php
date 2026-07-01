<?php

class Historial
{
    public function __construct(
        private int $idHistorial,
        private int $idUsuario,
        private int $idCancion,
        private string $fechaReproduccion
    ) {
    }

    public function getIdHistorial(): int
    {
        return $this->idHistorial;
    }

    public function getIdUsuario(): int
    {
        return $this->idUsuario;
    }

    public function getIdCancion(): int
    {
        return $this->idCancion;
    }

    public function getFechaReproduccion(): string
    {
        return $this->fechaReproduccion;
    }
}
