<?php
// Buscador simple por cancion, artista o album.
require_once __DIR__ . '/../app/helpers/funciones.php';
require_once __DIR__ . '/../app/dao/CancionDAO.php';

$logueado = estaLogueado();

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
            <h1>Buscar música</h1>
        </section>

        <form class="form-busqueda" method="GET">
            <input type="search" name="q" value="<?= limpiar($texto) ?>" placeholder="Canción, artista o álbum">
            <button class="boton boton-principal" type="submit">Buscar</button>
        </form>

        <section class="lista-canciones">
            <?php if ($texto !== '' && !$resultados): ?>
                <p class="texto-suave">No se encontraron resultados.</p>
            <?php endif; ?>

            <?php foreach ($resultados as $cancion): ?>
                <?php $origen = obtenerOrigenCancion($cancion['ruta_archivo_mp3'] ?? ''); ?>
                <!-- Resultado listo para ser enviado al reproductor JS. -->
                <article
                    class="fila-cancion js-cancion"
                    data-id="<?= (int) $cancion['id_cancion'] ?>"
                    data-title="<?= limpiar($cancion['titulo']) ?>"
                    data-artist="<?= limpiar($cancion['artista'] ?? '') ?>"
                    data-cover="<?= limpiar($cancion['ruta_portada'] ?? '') ?>"
                    data-src="<?= $logueado ? limpiar($cancion['ruta_archivo_mp3']) : '' ?>"
                >
                    <img src="<?= limpiar($cancion['ruta_portada'] ?? '') ?>" alt="">
                    <div>
                        <strong><?= limpiar($cancion['titulo']) ?></strong>
                        <span>
                            <?= limpiar($cancion['artista'] ?? '') ?> - <?= limpiar($cancion['album'] ?? '') ?>
                            <span class="indicador-origen <?= limpiar($origen['clase']) ?>" title="<?= limpiar($origen['titulo']) ?>"><?= limpiar($origen['texto']) ?></span>
                        </span>
                    </div>
                    <div class="acciones-cancion">
                        <?php if ($logueado): ?>
                            <button class="boton-reproducir js-reproducir-cancion" type="button">Reproducir</button>
                            <button class="boton-reproducir boton-fila js-agregar-cola" type="button" data-id-cancion="<?= (int) $cancion['id_cancion'] ?>">Agregar a fila</button>
                        <?php else: ?>
                            <a class="boton-reproducir" href="login.php">Reproducir</a>
                            <a class="boton-reproducir boton-fila" href="login.php">Agregar a fila</a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>

    <?php if ($logueado) include __DIR__ . '/partials/player.php'; ?>
    <script src="js/app.js"></script>
</body>
</html>


