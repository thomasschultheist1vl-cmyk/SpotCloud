<?php

class Album
{
    public function __construct(
        private int $idAlbum,
        private string $titulo,
        private ?int $anio,
        private ?int $idArtista,
        private ?string $rutaPortada
    ) {
    }

    public function getIdAlbum(): int
    {
        return $this->idAlbum;
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function getAnio(): ?int
    {
        return $this->anio;
    }

    public function getIdArtista(): ?int
    {
        return $this->idArtista;
    }

    public function getRutaPortada(): ?string
    {
        return $this->rutaPortada;
    }
}
