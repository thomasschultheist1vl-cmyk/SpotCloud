<?php

class Artista
{
    public function __construct(
        private int $idArtista,
        private string $nombre,
        private ?string $biografiaCorta,
        private ?string $genero
    ) {
    }

    public function getIdArtista(): int
    {
        return $this->idArtista;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getBiografiaCorta(): ?string
    {
        return $this->biografiaCorta;
    }

    public function getGenero(): ?string
    {
        return $this->genero;
    }
}
