<?php

require_once __DIR__ . '/../config/Conexion.php';

class HistorialDAO
{
    private PDO $db;

    public function __construct()
    {
        // DAO dedicado al registro de reproducciones del usuario.
        $this->db = (new Conexion())->conectar();
    }

    // Guarda una reproduccion cuando el usuario toca play.
    public function guardar(int $idUsuario, int $idCancion): bool
    {
        $sql = "INSERT INTO historial (id_usuario, id_cancion)
                VALUES (:id_usuario, :id_cancion)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id_usuario' => $idUsuario,
            'id_cancion' => $idCancion,
        ]);
    }

    // Muestra las ultimas canciones reproducidas por el usuario logueado.
    public function obtenerPorUsuario(int $idUsuario): array
    {
        $sql = "SELECT historial.fecha_reproduccion, canciones.titulo, albumes.ruta_portada, artistas.nombre AS artista
                FROM historial
                INNER JOIN canciones ON historial.id_cancion = canciones.id_cancion
                LEFT JOIN albumes ON canciones.id_album = albumes.id_album
                LEFT JOIN artistas ON albumes.id_artista = artistas.id_artista
                WHERE historial.id_usuario = :id_usuario
                ORDER BY historial.fecha_reproduccion DESC
                LIMIT 8";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_usuario' => $idUsuario]);

        return $stmt->fetchAll();
    }
}
