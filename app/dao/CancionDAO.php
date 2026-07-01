<?php

require_once __DIR__ . '/../config/Conexion.php';

class CancionDAO
{
    private PDO $db;

    public function __construct()
    {
        // La conexion se guarda para reutilizarla en todos los metodos.
        $this->db = (new Conexion())->conectar();
    }

    // Lista canciones con datos de album y artista para la pantalla principal.
    public function obtenerTodasConDatos(): array
    {
        $sql = "SELECT canciones.*, albumes.titulo AS album, albumes.ruta_portada, artistas.nombre AS artista
                FROM canciones
                LEFT JOIN albumes ON canciones.id_album = albumes.id_album
                LEFT JOIN artistas ON albumes.id_artista = artistas.id_artista
                ORDER BY artistas.nombre, albumes.titulo, canciones.id_cancion";

        return $this->db->query($sql)->fetchAll();
    }

    // Trae solamente las canciones que pertenecen a un album.
    public function obtenerPorAlbum(int $idAlbum): array
    {
        $sql = "SELECT canciones.*, albumes.ruta_portada, artistas.nombre AS artista
                FROM canciones
                LEFT JOIN albumes ON canciones.id_album = albumes.id_album
                LEFT JOIN artistas ON albumes.id_artista = artistas.id_artista
                WHERE canciones.id_album = :id_album
                ORDER BY canciones.id_cancion";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_album' => $idAlbum]);

        return $stmt->fetchAll();
    }

    // Busca una cancion especifica por su id.
    public function obtenerPorId(int $idCancion): ?array
    {
        $sql = "SELECT canciones.*, albumes.titulo AS album, albumes.ruta_portada, artistas.nombre AS artista
                FROM canciones
                LEFT JOIN albumes ON canciones.id_album = albumes.id_album
                LEFT JOIN artistas ON albumes.id_artista = artistas.id_artista
                WHERE canciones.id_cancion = :id_cancion";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_cancion' => $idCancion]);
        $cancion = $stmt->fetch();

        return $cancion ?: null;
    }

    // Busca por titulo de cancion, album o artista.
    public function buscar(string $texto): array
    {
        $sql = "SELECT canciones.*, albumes.titulo AS album, albumes.ruta_portada, artistas.nombre AS artista
                FROM canciones
                LEFT JOIN albumes ON canciones.id_album = albumes.id_album
                LEFT JOIN artistas ON albumes.id_artista = artistas.id_artista
                WHERE canciones.titulo LIKE :texto
                   OR albumes.titulo LIKE :texto
                   OR artistas.nombre LIKE :texto
                ORDER BY canciones.titulo";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['texto' => '%' . $texto . '%']);

        return $stmt->fetchAll();
    }

    // Guarda una cancion nueva con su URL de Supabase.
    public function crear(string $titulo, ?string $duracion, int $idAlbum, string $rutaArchivoMp3): bool
    {
        $sql = "INSERT INTO canciones (titulo, duracion, id_album, ruta_archivo_mp3)
                VALUES (:titulo, :duracion, :id_album, :ruta_archivo_mp3)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'titulo' => $titulo,
            'duracion' => $duracion,
            'id_album' => $idAlbum,
            'ruta_archivo_mp3' => $rutaArchivoMp3,
        ]);
    }
}
