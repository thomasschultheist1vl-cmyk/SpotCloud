<?php

class ColaReproduccion
{
    public function __construct(
        private int $idCola,
        private int $idUsuario
    ) {
    }

    public function getIdCola(): int
    {
        return $this->idCola;
    }

    public function getIdUsuario(): int
    {
        return $this->idUsuario;
    }
}
