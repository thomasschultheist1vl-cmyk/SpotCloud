<?php
// Muestra la fila de reproduccion personal del usuario conectado.
require_once __DIR__ . '/../app/helpers/funciones.php';
require_once __DIR__ . '/../app/dao/ColaReproduccionDAO.php';

protegerPagina();

$usuario = usuarioActual();
$colaDAO = new ColaReproduccionDAO();
$cancionesFila = $colaDAO->obtenerCancionesPorUsuario((int) $usuario['id_usuario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fila de reproduccion - SpotCloud</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <?php include __DIR__ . '/partials/nav.php'; ?>

    <main class="contenedor angosto">
        <section class="encabezado-seccion">
            <div>
                <p class="etiqueta">Tu musica</p>
                <h1>Fila de reproduccion</h1>
            </div>
            <?php if ($cancionesFila): ?>
                <button class="boton boton-secundario js-limpiar-cola" type="button">Vaciar fila</button>
            <?php endif; ?>
        </section>

        <section class="lista-canciones lista-fila" data-lista-cola>
            <?php if (!$cancionesFila): ?>
                <p class="texto-suave fila-vacia">Todavia no agregaste canciones a tu fila.</p>
            <?php endif; ?>

            <?php foreach ($cancionesFila as $cancion): ?>
                <?php $origen = obtenerOrigenCancion($cancion['ruta_archivo_mp3'] ?? ''); ?>
                <!-- Esta fila tambien puede reproducirse con la barra global. -->
                <article
                    class="fila-cancion js-cancion"
                    data-id="<?= (int) $cancion['id_cancion'] ?>"
                    data-title="<?= limpiar($cancion['titulo']) ?>"
                    data-artist="<?= limpiar($cancion['artista'] ?? '') ?>"
                    data-cover="<?= limpiar($cancion['ruta_portada'] ?? '') ?>"
                    data-src="<?= limpiar($cancion['ruta_archivo_mp3']) ?>"
                    data-en-cola="1"
                >
                    <span class="numero-cancion"><?= (int) $cancion['orden'] ?></span>
                    <div>
                        <strong><?= limpiar($cancion['titulo']) ?></strong>
                        <span>
                            <?= limpiar($cancion['artista'] ?? '') ?> - <?= limpiar($cancion['album'] ?? '') ?>
                            <span class="indicador-origen <?= limpiar($origen['clase']) ?>" title="<?= limpiar($origen['titulo']) ?>"><?= limpiar($origen['texto']) ?></span>
                        </span>
                    </div>
                    <div class="acciones-cancion">
                        <button class="boton-reproducir js-reproducir-cancion" type="button">Reproducir</button>
                        <button class="boton-reproducir boton-fila js-quitar-cola" type="button" data-id-cancion="<?= (int) $cancion['id_cancion'] ?>">Quitar</button>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>

    <?php include __DIR__ . '/partials/player.php'; ?>
    <script src="js/app.js"></script>
</body>
</html>