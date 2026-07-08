<?php
// Panel simple para cargar contenido en la base de datos.
require_once __DIR__ . '/../app/helpers/funciones.php';
require_once __DIR__ . '/../app/dao/AlbumDAO.php';
require_once __DIR__ . '/../app/dao/ArtistaDAO.php';
require_once __DIR__ . '/../app/dao/CancionDAO.php';

protegerPagina();

// DAO necesarios para crear y listar opciones de los formularios.
$albumDAO = new AlbumDAO();
$artistaDAO = new ArtistaDAO();
$cancionDAO = new CancionDAO();
$mensaje = '';
$error = '';
$seccionActiva = 'artista';

// Guarda archivos subidos desde el formulario y devuelve la ruta para la base de datos.
function guardarArchivoSubido(string $campo, string $carpeta, array $extensionesPermitidas, string $nombreCampo): string
{
    if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) {
        throw new Exception("Selecciona un archivo para {$nombreCampo}.");
    }

    if ($_FILES[$campo]['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("No se pudo subir {$nombreCampo}.");
    }

    $nombreOriginal = $_FILES[$campo]['name'];
    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

    if (!in_array($extension, $extensionesPermitidas, true)) {
        throw new Exception("El formato de {$nombreCampo} no es válido.");
    }

    $nombreBase = pathinfo($nombreOriginal, PATHINFO_FILENAME);
    $nombreBase = preg_replace('/[^a-zA-Z0-9_-]/', '-', $nombreBase);
    $nombreBase = trim($nombreBase, '-');

    if ($nombreBase === '') {
        $nombreBase = 'archivo';
    }

    $nombreFinal = $nombreBase . '-' . time() . '-' . random_int(1000, 9999) . '.' . $extension;
    $carpetaDestino = __DIR__ . '/uploads/' . $carpeta;

    if (!is_dir($carpetaDestino)) {
        mkdir($carpetaDestino, 0777, true);
    }

    $rutaDestino = $carpetaDestino . '/' . $nombreFinal;

    if (!move_uploaded_file($_FILES[$campo]['tmp_name'], $rutaDestino)) {
        throw new Exception("No se pudo guardar {$nombreCampo} en el servidor.");
    }

    return 'uploads/' . $carpeta . '/' . $nombreFinal;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cada formulario envia una accion distinta en un input hidden.
    $accion = $_POST['accion'] ?? '';
    $seccionesPorAccion = [
        'artista' => 'artista',
        'album' => 'album',
        'cancion' => 'cancion',
        'eliminar_albumes' => 'eliminar-albumes',
        'eliminar_canciones' => 'eliminar-canciones',
    ];
    $seccionActiva = $seccionesPorAccion[$accion] ?? $seccionActiva;

    try {
        // Alta de artista.
        if ($accion === 'artista') {
            $artistaDAO->crear(trim($_POST['nombre']), trim($_POST['biografia']), trim($_POST['genero']));
            $mensaje = 'Artista cargado correctamente.';
            $seccionActiva = 'album';
        }

        // Alta de album con portada subida desde el equipo o celular.
        if ($accion === 'album') {
            $rutaPortada = guardarArchivoSubido('portada', 'portadas', ['jpg', 'jpeg', 'png', 'webp'], 'la portada');
            $albumDAO->crear(trim($_POST['titulo']), (int) $_POST['anio'], (int) $_POST['id_artista'], $rutaPortada);
            $mensaje = 'Álbum cargado correctamente.';
            $seccionActiva = 'cancion';
        }

        // Alta de cancion con archivo MP3 subido desde el equipo o celular.
        if ($accion === 'cancion') {
            $seccionActiva = 'cancion';
            $rutaMp3 = guardarArchivoSubido('archivo_mp3', 'canciones', ['mp3'], 'el archivo MP3');
            $cancionDAO->crear(trim($_POST['titulo']), null, (int) $_POST['id_album'], $rutaMp3);
            $mensaje = 'Canción cargada correctamente.';
        }

        // Baja de canciones seleccionadas.
        if ($accion === 'eliminar_canciones') {
            $seccionActiva = 'eliminar-canciones';
            $cantidad = $cancionDAO->eliminarVarios($_POST['ids_canciones'] ?? []);
            $mensaje = $cantidad > 0 ? 'Canciones eliminadas correctamente.' : 'Selecciona al menos una canción.';
        }

        // Baja de albumes seleccionados. Sus canciones se eliminan por cascada.
        if ($accion === 'eliminar_albumes') {
            $seccionActiva = 'eliminar-albumes';
            $cantidad = $albumDAO->eliminarVarios($_POST['ids_albumes'] ?? []);
            $mensaje = $cantidad > 0 ? 'Álbumes eliminados correctamente.' : 'Selecciona al menos un álbum.';
        }
    } catch (Exception $e) {
        $error = 'No se pudo completar la acción: ' . $e->getMessage();
    }
}

// Se usan para llenar los select y las listas de administracion.
$albumes = $albumDAO->obtenerTodos();
$artistas = $artistaDAO->obtenerTodos();
$canciones = $cancionDAO->obtenerTodasConDatos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - SpotCloud</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <?php include __DIR__ . '/partials/nav.php'; ?>

    <main class="contenedor">
        <section class="titulo-pagina">
            <p class="etiqueta">Administración</p>
            <h1>Cargar contenido</h1>
        </section>

        <?php if ($mensaje): ?>
            <p class="alerta ok"><?= limpiar($mensaje) ?></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="alerta error"><?= limpiar($error) ?></p>
        <?php endif; ?>

        <section class="menu-admin-pasos" aria-label="Opciones de administración">
            <button class="paso-admin <?= $seccionActiva === 'artista' ? 'activo' : '' ?>" type="button" data-seccion="artista">
                <span>1</span>
                <strong>Artista</strong>
                <small>Crear artista</small>
            </button>

            <button class="paso-admin <?= $seccionActiva === 'album' ? 'activo' : '' ?>" type="button" data-seccion="album">
                <span>2</span>
                <strong>Álbum</strong>
                <small>Cargar portada</small>
            </button>

            <button class="paso-admin <?= $seccionActiva === 'cancion' ? 'activo' : '' ?>" type="button" data-seccion="cancion">
                <span>3</span>
                <strong>Canción</strong>
                <small>Subir MP3</small>
            </button>

            <button class="paso-admin <?= $seccionActiva === 'eliminar-albumes' ? 'activo' : '' ?>" type="button" data-seccion="eliminar-albumes">
                <span>4</span>
                <strong>Álbumes</strong>
                <small>Eliminar</small>
            </button>

            <button class="paso-admin <?= $seccionActiva === 'eliminar-canciones' ? 'activo' : '' ?>" type="button" data-seccion="eliminar-canciones">
                <span>5</span>
                <strong>Canciones</strong>
                <small>Eliminar</small>
            </button>
        </section>

        <section class="secciones-admin">
            <!-- Paso 1: insertar artistas. -->
            <form class="panel formulario panel-paso seccion-admin <?= $seccionActiva === 'artista' ? 'activa' : '' ?>" data-panel="artista" method="POST">
                <input type="hidden" name="accion" value="artista">
                <h2>Nuevo artista</h2>
                <p class="texto-suave">Primero cargá el artista. Después podés crear sus álbumes.</p>
                <label>Nombre</label>
                <input type="text" name="nombre" required>
                <label>Biografía corta</label>
                <textarea name="biografia" rows="3" maxlength="180"></textarea>
                <small class="ayuda-campo">Máximo 180 caracteres.</small>
                <label>Género</label>
                <input type="text" name="genero">
                <button class="boton boton-principal" type="submit">Guardar artista</button>
            </form>

            <!-- Paso 2: insertar albumes. -->
            <form class="panel formulario panel-paso seccion-admin <?= $seccionActiva === 'album' ? 'activa' : '' ?>" data-panel="album" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="album">
                <h2>Nuevo álbum</h2>
                <p class="texto-suave">Elegí el artista y subí la portada del álbum.</p>
                <label>Título</label>
                <input type="text" name="titulo" required>
                <label>Año</label>
                <input type="number" name="anio" min="1900" max="2100">
                <label>Artista</label>
                <select name="id_artista" required>
                    <?php foreach ($artistas as $artista): ?>
                        <option value="<?= (int) $artista['id_artista'] ?>"><?= limpiar($artista['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Portada</label>
                <label class="selector-archivo">
                    <input class="input-archivo" type="file" name="portada" accept="image/jpeg,image/png,image/webp" data-texto-archivo="texto-portada" required>
                    <span>Seleccionar archivo</span>
                </label>
                <small class="texto-archivo" id="texto-portada">Sin archivos seleccionados.</small>
                <small class="ayuda-campo">El navegador abre el explorador de archivos y guarda la ruta automáticamente.</small>
                <button class="boton boton-principal" type="submit">Guardar álbum</button>
            </form>

            <!-- Paso 3: insertar canciones. -->
            <form class="panel formulario panel-paso seccion-admin <?= $seccionActiva === 'cancion' ? 'activa' : '' ?>" data-panel="cancion" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="cancion">
                <h2>Nueva canción</h2>
                <p class="texto-suave">Seleccioná el álbum y subí el archivo MP3.</p>
                <label>Título</label>
                <input type="text" name="titulo" required>
                <label>Álbum</label>
                <select name="id_album" required>
                    <?php foreach ($albumes as $album): ?>
                        <option value="<?= (int) $album['id_album'] ?>"><?= limpiar($album['titulo']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Archivo MP3</label>
                <label class="selector-archivo">
                    <input class="input-archivo" type="file" name="archivo_mp3" accept="audio/mpeg,audio/mp3" data-texto-archivo="texto-cancion" required>
                    <span>Seleccionar archivo</span>
                </label>
                <small class="texto-archivo" id="texto-cancion">Sin archivos seleccionados.</small>
                <small class="ayuda-campo">En computadora abre el explorador de Windows; en celular abre los archivos del dispositivo.</small>
                <button class="boton boton-principal" type="submit">Guardar canción</button>
            </form>

            <!-- Paso 4: baja de albumes. Al borrar un album tambien se borran sus canciones. -->
            <form class="panel formulario panel-paso panel-paso-ancho seccion-admin <?= $seccionActiva === 'eliminar-albumes' ? 'activa' : '' ?>" data-panel="eliminar-albumes" method="POST" onsubmit="return confirm('¿Seguro que querés eliminar los álbumes seleccionados?');">
                <input type="hidden" name="accion" value="eliminar_albumes">
                <h2>Eliminar álbumes</h2>
                <p class="texto-suave">Si borrás un álbum, también se borran sus canciones.</p>

                <div class="lista-checkbox">
                    <?php foreach ($albumes as $album): ?>
                        <label class="opcion-checkbox">
                            <input type="checkbox" name="ids_albumes[]" value="<?= (int) $album['id_album'] ?>">
                            <span>
                                <strong><?= limpiar($album['titulo']) ?></strong>
                                <small><?= limpiar($album['artista'] ?? 'Sin artista') ?></small>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <button class="boton boton-peligro" type="submit">Eliminar álbumes</button>
            </form>

            <!-- Paso 5: baja de canciones individuales. -->
            <form class="panel formulario panel-paso panel-paso-ancho seccion-admin <?= $seccionActiva === 'eliminar-canciones' ? 'activa' : '' ?>" data-panel="eliminar-canciones" method="POST" onsubmit="return confirm('¿Seguro que querés eliminar las canciones seleccionadas?');">
                <input type="hidden" name="accion" value="eliminar_canciones">
                <h2>Eliminar canciones</h2>
                <p class="texto-suave">Seleccioná canciones puntuales sin borrar todo el álbum.</p>

                <div class="lista-checkbox">
                    <?php foreach ($canciones as $cancion): ?>
                        <?php $origen = obtenerOrigenCancion($cancion['ruta_archivo_mp3'] ?? ''); ?>
                        <label class="opcion-checkbox">
                            <input type="checkbox" name="ids_canciones[]" value="<?= (int) $cancion['id_cancion'] ?>">
                            <span>
                                <strong><?= limpiar($cancion['titulo']) ?></strong>
                                <small>
                                    <?= limpiar($cancion['artista'] ?? 'Sin artista') ?> - <?= limpiar($cancion['album'] ?? 'Sin álbum') ?>
                                    <span class="indicador-origen <?= limpiar($origen['clase']) ?>" title="<?= limpiar($origen['titulo']) ?>"><?= limpiar($origen['texto']) ?></span>
                                </small>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <button class="boton boton-peligro" type="submit">Eliminar canciones</button>
            </form>
        </section>
    </main>

    <?php include __DIR__ . '/partials/player.php'; ?>
    <script src="js/app.js"></script>
    <script src="js/admin.js"></script>
</body>
</html>


