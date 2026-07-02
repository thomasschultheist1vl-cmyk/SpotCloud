<?php
// Detalle de album: muestra datos del album y sus canciones.
require_once __DIR__ . '/../app/helpers/funciones.php';
require_once __DIR__ . '/../app/dao/AlbumDAO.php';
require_once __DIR__ . '/../app/dao/CancionDAO.php';

protegerPagina();

// El id llega por URL: album.php?id=1
$idAlbum = (int) ($_GET['id'] ?? 0);
$albumDAO = new AlbumDAO();
$cancionDAO = new CancionDAO();

$album = $albumDAO->obtenerPorId($idAlbum);

// Si el id no existe, cortamos la carga con error 404.
if (!$album) {
    http_response_code(404);
    die('Album no encontrado.');
}

// Canciones relacionadas con el album seleccionado.
$canciones = $cancionDAO->obtenerPorAlbum($idAlbum);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= limpiar($album['titulo']) ?> - SpotCloud</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <?php include __DIR__ . '/partials/nav.php'; ?>

    <main class="contenedor">
        <section class="portada-album">
            <img src="<?= limpiar($album['ruta_portada'] ?? '') ?>" alt="Portada de <?= limpiar($album['titulo']) ?>">
            <div>
                <p class="etiqueta">Album</p>
                <h1><?= limpiar($album['titulo']) ?></h1>
                <p>
                    <?= limpiar($album['artista'] ?? 'Sin artista') ?>
                    <?php if ($album['anio']): ?>
                        - <?= (int) $album['anio'] ?>
                    <?php endif; ?>
                    <?php if ($album['genero']): ?>
                        - <?= limpiar($album['genero']) ?>
                    <?php endif; ?>
                </p>
            </div>
        </section>

        <section class="lista-canciones canciones-album">
            <?php foreach ($canciones as $numero => $cancion): ?>
                <?php $origen = obtenerOrigenCancion($cancion['ruta_archivo_mp3'] ?? ''); ?>
                <!-- Cada fila se puede reproducir desde la barra inferior. -->
                <article
                    class="fila-cancion js-cancion"
                    data-id="<?= (int) $cancion['id_cancion'] ?>"
                    data-title="<?= limpiar($cancion['titulo']) ?>"
                    data-artist="<?= limpiar($cancion['artista'] ?? '') ?>"
                    data-cover="<?= limpiar($cancion['ruta_portada'] ?? $album['ruta_portada'] ?? '') ?>"
                    data-src="<?= limpiar($cancion['ruta_archivo_mp3']) ?>"
                >
                    <span class="numero-cancion"><?= $numero + 1 ?></span>
                    <div>
                        <strong><?= limpiar($cancion['titulo']) ?></strong>
                        <span>
                            <?= limpiar($cancion['artista'] ?? '') ?>
                            <span class="indicador-origen <?= limpiar($origen['clase']) ?>" title="<?= limpiar($origen['titulo']) ?>"><?= limpiar($origen['texto']) ?></span>
                        </span>
                    </div>
                    <span class="duracion"><?= limpiar(formatearDuracion($cancion['duracion'])) ?></span>
                    <button class="boton-reproducir js-reproducir-cancion" type="button">Reproducir</button>
                </article>
            <?php endforeach; ?>
        </section>
    </main>

    <?php include __DIR__ . '/partials/player.php'; ?>
    <script src="js/app.js"></script>
</body>
</html>


