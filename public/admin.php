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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cada formulario envia una accion distinta en un input hidden.
    $accion = $_POST['accion'] ?? '';

    try {
        // Alta de artista.
        if ($accion === 'artista') {
            $artistaDAO->crear(trim($_POST['nombre']), trim($_POST['biografia']), trim($_POST['genero']));
            $mensaje = 'Artista cargado correctamente.';
        }

        // Alta de album con URL de portada.
        if ($accion === 'album') {
            $albumDAO->crear(trim($_POST['titulo']), (int) $_POST['anio'], (int) $_POST['id_artista'], trim($_POST['ruta_portada']));
            $mensaje = 'Album cargado correctamente.';
        }

        // Alta de cancion con URL del archivo MP3 en Supabase.
        if ($accion === 'cancion') {
            $cancionDAO->crear(trim($_POST['titulo']), trim($_POST['duracion']), (int) $_POST['id_album'], trim($_POST['ruta_archivo_mp3']));
            $mensaje = 'Cancion cargada correctamente.';
        }
    } catch (Exception $e) {
        $error = 'No se pudo guardar: ' . $e->getMessage();
    }
}

// Se usan para llenar los select de artistas y albumes.
$albumes = $albumDAO->obtenerTodos();
$artistas = $artistaDAO->obtenerTodos();
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
            <p class="etiqueta">Administracion</p>
            <h1>Cargar contenido</h1>
        </section>

        <?php if ($mensaje): ?>
            <p class="alerta ok"><?= limpiar($mensaje) ?></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="alerta error"><?= limpiar($error) ?></p>
        <?php endif; ?>

        <section class="grilla-admin">
            <!-- Formulario para insertar artistas. -->
            <form class="panel formulario" method="POST">
                <input type="hidden" name="accion" value="artista">
                <h2>Nuevo artista</h2>
                <label>Nombre</label>
                <input type="text" name="nombre" required>
                <label>Biografia corta</label>
                <textarea name="biografia" rows="3"></textarea>
                <label>Genero</label>
                <input type="text" name="genero">
                <button class="boton boton-principal" type="submit">Guardar artista</button>
            </form>

            <!-- Formulario para insertar albumes. -->
            <form class="panel formulario" method="POST">
                <input type="hidden" name="accion" value="album">
                <h2>Nuevo album</h2>
                <label>Titulo</label>
                <input type="text" name="titulo" required>
                <label>Anio</label>
                <input type="number" name="anio" min="1900" max="2100">
                <label>Artista</label>
                <select name="id_artista" required>
                    <?php foreach ($artistas as $artista): ?>
                        <option value="<?= (int) $artista['id_artista'] ?>"><?= limpiar($artista['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>URL de portada</label>
                <input type="url" name="ruta_portada" placeholder="https://supabase.com/..." required>
                <button class="boton boton-principal" type="submit">Guardar album</button>
            </form>

            <!-- Formulario para insertar canciones. -->
            <form class="panel formulario" method="POST">
                <input type="hidden" name="accion" value="cancion">
                <h2>Nueva cancion</h2>
                <label>Titulo</label>
                <input type="text" name="titulo" required>
                <label>Duracion</label>
                <input type="text" name="duracion" placeholder="00:03:30">
                <label>Album</label>
                <select name="id_album" required>
                    <?php foreach ($albumes as $album): ?>
                        <option value="<?= (int) $album['id_album'] ?>"><?= limpiar($album['titulo']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>URL del MP3</label>
                <input type="url" name="ruta_archivo_mp3" placeholder="https://supabase.com/..." required>
                <button class="boton boton-principal" type="submit">Guardar cancion</button>
            </form>
        </section>
    </main>
</body>
</html>


