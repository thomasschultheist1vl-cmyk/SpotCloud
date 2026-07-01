<?php

require_once __DIR__ . '/../config/Conexion.php';

class ArtistaDAO
{
    private PDO $db;

    public function __construct()
    {
        // Conexion compartida para las consultas de artistas.
        $this->db = (new Conexion())->conectar();
    }

    // Devuelve todos los artistas para listarlos en formularios y vistas.
    public function obtenerTodos(): array
    {
        $sql = "SELECT * FROM artistas ORDER BY nombre";
        return $this->db->query($sql)->fetchAll();
    }

    // Carga un artista nuevo desde administracion.
    public function crear(string $nombre, ?string $biografiaCorta, ?string $genero): bool
    {
        $sql = "INSERT INTO artistas (nombre, biografia_corta, genero)
                VALUES (:nombre, :biografia_corta, :genero)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'nombre' => $nombre,
            'biografia_corta' => $biografiaCorta,
            'genero' => $genero,
        ]);
    }
}
