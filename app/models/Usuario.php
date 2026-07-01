<?php

class Usuario
{
    public function __construct(
        private int $idUsuario,
        private string $nombre,
        private string $correo,
        private string $password
    ) {
    }

    public function getIdUsuario(): int
    {
        return $this->idUsuario;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getCorreo(): string
    {
        return $this->correo;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
