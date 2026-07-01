<?php

class Cancion
{
    public function __construct(
        private int $idCancion,
        private string $titulo,
        private ?string $duracion,
        private ?int $idAlbum,
        private string $rutaArchivoMp3
    ) {
    }

    public function getIdCancion(): int
    {
        return $this->idCancion;
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function getDuracion(): ?string
    {
        return $this->duracion;
    }

    public function getIdAlbum(): ?int
    {
        return $this->idAlbum;
    }

    public function getRutaArchivoMp3(): string
    {
        return $this->rutaArchivoMp3;
    }
}
