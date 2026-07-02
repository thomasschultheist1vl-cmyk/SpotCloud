<?php

require_once __DIR__ . '/../config/Conexion.php';

class AlbumDAO
{
    private PDO $db;

    public function __construct()
    {
        // Cada DAO recibe la misma conexion PDO para consultar la base.
        $this->db = (new Conexion())->conectar();
    }

    // Trae todos los albumes junto con el nombre de su artista.
    public function obtenerTodos(): array
    {
        $sql = "SELECT albumes.*, artistas.nombre AS artista
                FROM albumes
                LEFT JOIN artistas ON albumes.id_artista = artistas.id_artista
                ORDER BY albumes.titulo";

        return $this->db->query($sql)->fetchAll();
    }

    // Busca un album puntual para mostrar su pagina de detalle.
    public function obtenerPorId(int $idAlbum): ?array
    {
        $sql = "SELECT albumes.*, artistas.nombre AS artista, artistas.genero
                FROM albumes
                LEFT JOIN artistas ON albumes.id_artista = artistas.id_artista
                WHERE albumes.id_album = :id_album";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_album' => $idAlbum]);
        $album = $stmt->fetch();

        return $album ?: null;
    }

    // Inserta un album nuevo desde el panel de administracion.
    public function crear(string $titulo, ?int $anio, int $idArtista, string $rutaPortada): bool
    {
        $sql = "INSERT INTO albumes (titulo, anio, id_artista, ruta_portada)
                VALUES (:titulo, :anio, :id_artista, :ruta_portada)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'titulo' => $titulo,
            'anio' => $anio,
            'id_artista' => $idArtista,
            'ruta_portada' => $rutaPortada,
        ]);
    }

    // Elimina varios albumes. Sus canciones se borran por la relacion en cascada.
    public function eliminarVarios(array $idsAlbumes): int
    {
        $idsAlbumes = array_filter(array_map('intval', $idsAlbumes));

        if (!$idsAlbumes) {
            return 0;
        }

        $marcadores = implode(',', array_fill(0, count($idsAlbumes), '?'));
        $sql = "DELETE FROM albumes WHERE id_album IN ($marcadores)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($idsAlbumes));

        return $stmt->rowCount();
    }
}
