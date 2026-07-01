<?php
// Buscador simple por cancion, artista o album.
require_once __DIR__ . '/../app/helpers/funciones.php';
require_once __DIR__ . '/../app/dao/CancionDAO.php';

protegerPagina();

$texto = trim($_GET['q'] ?? '');
$cancionDAO = new CancionDAO();

// Si no se escribio nada, no se consulta la base todavia.
$resultados = $texto === '' ? [] : $cancionDAO->buscar($texto);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar - SpotCloud</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <?php include __DIR__ . '/partials/nav.php'; ?>

    <main class="contenedor angosto">
        <section class="titulo-pagina">
            <p class="etiqueta">Explorar</p>
            <h1>Buscar musica</h1>
        </section>

        <form class="form-busqueda" method="GET">
            <input type="search" name="q" value="<?= limpiar($texto) ?>" placeholder="Cancion, artista o album">
            <button class="boton boton-principal" type="submit">Buscar</button>
        </form>

        <section class="lista-canciones">
            <?php if ($texto !== '' && !$resultados): ?>
                <p class="texto-suave">No se encontraron resultados.</p>
            <?php endif; ?>

            <?php foreach ($resultados as $cancion): ?>
                <!-- Resultado listo para ser enviado al reproductor JS. -->
                <article
                    class="fila-cancion js-cancion"
                    data-id="<?= (int) $cancion['id_cancion'] ?>"
                    data-title="<?= limpiar($cancion['titulo']) ?>"
                    data-artist="<?= limpiar($cancion['artista'] ?? '') ?>"
                    data-cover="<?= limpiar($cancion['ruta_portada'] ?? '') ?>"
                    data-src="<?= limpiar($cancion['ruta_archivo_mp3']) ?>"
                >
                    <img src="<?= limpiar($cancion['ruta_portada'] ?? '') ?>" alt="">
                    <div>
                        <strong><?= limpiar($cancion['titulo']) ?></strong>
                        <span><?= limpiar($cancion['artista'] ?? '') ?> - <?= limpiar($cancion['album'] ?? '') ?></span>
                    </div>
                    <button class="boton-reproducir js-reproducir-cancion" type="button">Reproducir</button>
                </article>
            <?php endforeach; ?>
        </section>
    </main>

    <?php include __DIR__ . '/partials/player.php'; ?>
    <script src="js/app.js"></script>
</body>
</html>


