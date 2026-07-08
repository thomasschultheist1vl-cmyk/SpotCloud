<?php

require_once __DIR__ . '/../config/Conexion.php';

class ColaReproduccionDAO
{
    private PDO $db;

    public function __construct()
    {
        // Reutilizamos la misma conexion PDO que el resto de los DAO.
        $this->db = (new Conexion())->conectar();
    }

    // Busca la fila del usuario. Si todavia no existe, la crea.
    public function obtenerOCrearPorUsuario(int $idUsuario): int
    {
        $stmt = $this->db->prepare('SELECT id_cola FROM cola_reproduccion WHERE id_usuario = :id_usuario LIMIT 1');
        $stmt->execute(['id_usuario' => $idUsuario]);
        $cola = $stmt->fetch();

        if ($cola) {
            return (int) $cola['id_cola'];
        }

        $stmt = $this->db->prepare('INSERT INTO cola_reproduccion (id_usuario) VALUES (:id_usuario)');
        $stmt->execute(['id_usuario' => $idUsuario]);

        return (int) $this->db->lastInsertId();
    }

    // Devuelve las canciones que el usuario agrego a su fila de reproduccion.
    public function obtenerCancionesPorUsuario(int $idUsuario): array
    {
        $sql = "SELECT cola_canciones.orden, cola_canciones.reproducida,
                       canciones.*, albumes.titulo AS album, albumes.ruta_portada, artistas.nombre AS artista
                FROM cola_reproduccion
                INNER JOIN cola_canciones ON cola_reproduccion.id_cola = cola_canciones.id_cola
                INNER JOIN canciones ON cola_canciones.id_cancion = canciones.id_cancion
                LEFT JOIN albumes ON canciones.id_album = albumes.id_album
                LEFT JOIN artistas ON albumes.id_artista = artistas.id_artista
                WHERE cola_reproduccion.id_usuario = :id_usuario
                ORDER BY cola_canciones.orden, canciones.id_cancion";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_usuario' => $idUsuario]);

        return $stmt->fetchAll();
    }

    // Agrega una cancion al final de la fila del usuario.
    public function agregarCancion(int $idUsuario, int $idCancion): array
    {
        if (!$this->existeCancion($idCancion)) {
            return ['ok' => false, 'mensaje' => 'La cancion no existe.'];
        }

        $idCola = $this->obtenerOCrearPorUsuario($idUsuario);

        if ($this->cancionYaEstaEnCola($idCola, $idCancion)) {
            return ['ok' => true, 'agregada' => false, 'mensaje' => 'Ya estaba en tu fila.'];
        }

        $orden = $this->proximoOrden($idCola);
        $sql = 'INSERT INTO cola_canciones (id_cola, id_cancion, orden, reproducida)
                VALUES (:id_cola, :id_cancion, :orden, 0)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_cola' => $idCola,
            'id_cancion' => $idCancion,
            'orden' => $orden,
        ]);

        return ['ok' => true, 'agregada' => true, 'mensaje' => 'Agregada a tu fila.'];
    }

    // Quita una cancion de la fila y reordena las que quedan.
    public function quitarCancion(int $idUsuario, int $idCancion): bool
    {
        $idCola = $this->obtenerOCrearPorUsuario($idUsuario);
        $stmt = $this->db->prepare('DELETE FROM cola_canciones WHERE id_cola = :id_cola AND id_cancion = :id_cancion');
        $stmt->execute([
            'id_cola' => $idCola,
            'id_cancion' => $idCancion,
        ]);

        $this->reordenar($idCola);

        return $stmt->rowCount() > 0;
    }

    // Vacia toda la fila de reproduccion del usuario.
    public function limpiarPorUsuario(int $idUsuario): int
    {
        $idCola = $this->obtenerOCrearPorUsuario($idUsuario);
        $stmt = $this->db->prepare('DELETE FROM cola_canciones WHERE id_cola = :id_cola');
        $stmt->execute(['id_cola' => $idCola]);

        return $stmt->rowCount();
    }

    private function existeCancion(int $idCancion): bool
    {
        $stmt = $this->db->prepare('SELECT id_cancion FROM canciones WHERE id_cancion = :id_cancion');
        $stmt->execute(['id_cancion' => $idCancion]);

        return (bool) $stmt->fetch();
    }

    private function cancionYaEstaEnCola(int $idCola, int $idCancion): bool
    {
        $sql = 'SELECT id_cancion FROM cola_canciones WHERE id_cola = :id_cola AND id_cancion = :id_cancion';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_cola' => $idCola,
            'id_cancion' => $idCancion,
        ]);

        return (bool) $stmt->fetch();
    }

    private function proximoOrden(int $idCola): int
    {
        $stmt = $this->db->prepare('SELECT COALESCE(MAX(orden), 0) + 1 AS proximo FROM cola_canciones WHERE id_cola = :id_cola');
        $stmt->execute(['id_cola' => $idCola]);
        $fila = $stmt->fetch();

        return (int) $fila['proximo'];
    }

    private function reordenar(int $idCola): void
    {
        $stmt = $this->db->prepare('SELECT id_cancion FROM cola_canciones WHERE id_cola = :id_cola ORDER BY orden, id_cancion');
        $stmt->execute(['id_cola' => $idCola]);
        $canciones = $stmt->fetchAll();

        $update = $this->db->prepare('UPDATE cola_canciones SET orden = :orden WHERE id_cola = :id_cola AND id_cancion = :id_cancion');

        foreach ($canciones as $index => $cancion) {
            $update->execute([
                'orden' => $index + 1,
                'id_cola' => $idCola,
                'id_cancion' => $cancion['id_cancion'],
            ]);
        }
    }
}