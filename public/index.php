<?php
// Pantalla principal: muestra el contenido musical despues del login.
require_once __DIR__ . '/../app/helpers/funciones.php';
require_once __DIR__ . '/../app/dao/AlbumDAO.php';
require_once __DIR__ . '/../app/dao/CancionDAO.php';
require_once __DIR__ . '/../app/dao/HistorialDAO.php';

// Si no hay sesion activa, no se permite entrar a la home.
protegerPagina();

$usuario = usuarioActual();

// Los DAO separan las consultas SQL de la vista HTML.
$albumDAO = new AlbumDAO();
$cancionDAO = new CancionDAO();
$historialDAO = new HistorialDAO();

// Datos que se usan para armar las secciones de la pagina.
$albumes = $albumDAO->obtenerTodos();
$canciones = $cancionDAO->obtenerTodasConDatos();
$destacada = $canciones[0] ?? null;
$historial = $historialDAO->obtenerPorUsuario((int) $usuario['id_usuario']);

// El slide se muestra solo una vez, justo despues del login.
$mostrarIntro = $_SESSION['mostrar_intro'] ?? false;
unset($_SESSION['mostrar_intro']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpotCloud</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <?php if ($mostrarIntro): ?>
        <!-- Splash heredado del prototipo: pantalla amarilla que sube al iniciar. -->
        <section class="pantalla-inicio" aria-label="Bienvenida a SpotCloud">
            <div class="contenido-inicio">
                <h1 class="texto-logo">SpotCloud</h1>
                <div class="cargador"></div>
            </div>
        </section>
    <?php endif; ?>

    <?php include __DIR__ . '/partials/nav.php'; ?>

    <main class="contenedor">
        <?php if ($destacada): ?>
            <!-- Cancion destacada de la portada. -->
            <section class="destacado">
                <img src="<?= limpiar($destacada['ruta_portada']) ?>" alt="Portada de <?= limpiar($destacada['album']) ?>">
                <div>
                    <p class="etiqueta">Cancion destacada</p>
                    <h1><?= limpiar($destacada['titulo']) ?></h1>
                    <p><?= limpiar($destacada['artista'] ?? 'Artista') ?> - <?= limpiar($destacada['album'] ?? 'Album') ?></p>
                    <a class="boton boton-principal" href="album.php?id=<?= (int) $destacada['id_album'] ?>">Ver album</a>
                </div>
            </section>
        <?php endif; ?>

        <section class="encabezado-seccion">
            <div>
                <p class="etiqueta">Tu biblioteca</p>
                <h2>Albumes disponibles</h2>
            </div>
            <a class="boton boton-secundario" href="admin.php">Cargar contenido</a>
        </section>

        <section class="grilla-albumes">
            <?php foreach ($albumes as $album): ?>
                <!-- Cada tarjeta lleva al detalle de su album. -->
                <a class="tarjeta-album" href="album.php?id=<?= (int) $album['id_album'] ?>">
                    <img src="<?= limpiar($album['ruta_portada'] ?? '') ?>" alt="Portada de <?= limpiar($album['titulo']) ?>">
                    <strong><?= limpiar($album['titulo']) ?></strong>
                    <span><?= limpiar($album['artista'] ?? 'Sin artista') ?></span>
                </a>
            <?php endforeach; ?>
        </section>

        <section class="dos-columnas">
            <div>
                <div class="encabezado-seccion compacto">
                    <div>
                        <p class="etiqueta">Explorar</p>
                        <h2>Canciones</h2>
                    </div>
                    <a class="link" href="buscar.php">Buscar</a>
                </div>

                <div class="lista-canciones">
                    <?php foreach (array_slice($canciones, 0, 12) as $cancion): ?>
                        <?php $origen = obtenerOrigenCancion($cancion['ruta_archivo_mp3'] ?? ''); ?>
                        <!-- Los data-* guardan la info que el JS usa para el reproductor. -->
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
                                <span>
                                    <?= limpiar($cancion['artista'] ?? '') ?>
                                    <span class="indicador-origen <?= limpiar($origen['clase']) ?>" title="<?= limpiar($origen['titulo']) ?>"><?= limpiar($origen['texto']) ?></span>
                                </span>
                            </div>
                            <div class="acciones-cancion">
                                <button class="boton-reproducir js-reproducir-cancion" type="button">Reproducir</button>
                                <button class="boton-reproducir boton-fila js-agregar-cola" type="button" data-id-cancion="<?= (int) $cancion['id_cancion'] ?>">Agregar a fila</button>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <aside class="panel-lateral panel-historial">
                <!-- Historial armado con la tabla historial de la base. -->
                <p class="etiqueta">Historial</p>
                <h2>Ultimas reproducciones</h2>
                <?php if (!$historial): ?>
                    <p class="texto-suave">Todavia no reproduciste canciones.</p>
                <?php endif; ?>

                <?php foreach ($historial as $item): ?>
                    <article class="item-historial">
                        <img src="<?= limpiar($item['ruta_portada'] ?? '') ?>" alt="">
                        <div>
                            <strong><?= limpiar($item['titulo']) ?></strong>
                            <span><?= limpiar($item['artista'] ?? '') ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </aside>
        </section>
    </main>

    <?php include __DIR__ . '/partials/player.php'; ?>
    <script src="js/app.js"></script>
</body>
</html>


