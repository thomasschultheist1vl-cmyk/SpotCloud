<?php
// Perfil del usuario: permite cambiar nombre y foto visible en la barra superior.
require_once __DIR__ . '/../app/helpers/funciones.php';
require_once __DIR__ . '/../app/dao/UsuarioDAO.php';

protegerPagina();

$usuario = usuarioActual();
$usuarioDAO = new UsuarioDAO();
$mensaje = '';
$error = '';

function guardarFotoPerfil(string $campo): ?string
{
    if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($_FILES[$campo]['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No se pudo subir la foto.');
    }

    if ($_FILES[$campo]['size'] > 2 * 1024 * 1024) {
        throw new Exception('La foto no puede superar los 2 MB.');
    }

    $infoImagen = getimagesize($_FILES[$campo]['tmp_name']);
    if ($infoImagen === false) {
        throw new Exception('El archivo elegido no parece ser una imagen valida.');
    }

    $nombreOriginal = $_FILES[$campo]['name'];
    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($extension, $permitidas, true)) {
        throw new Exception('La foto debe ser JPG, PNG o WEBP.');
    }

    $carpetaDestino = __DIR__ . '/uploads/perfiles';
    if (!is_dir($carpetaDestino)) {
        mkdir($carpetaDestino, 0777, true);
    }

    $nombreFinal = 'perfil-' . time() . '-' . random_int(1000, 9999) . '.' . $extension;
    $rutaDestino = $carpetaDestino . '/' . $nombreFinal;

    if (!move_uploaded_file($_FILES[$campo]['tmp_name'], $rutaDestino)) {
        throw new Exception('No se pudo guardar la foto en el servidor.');
    }

    return 'uploads/perfiles/' . $nombreFinal;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nombre = trim($_POST['nombre'] ?? '');

        if ($nombre === '') {
            throw new Exception('El nombre no puede estar vacio.');
        }

        $fotoActual = $usuario['foto_perfil'] ?? null;
        $fotoNueva = guardarFotoPerfil('foto_perfil');
        $fotoPerfil = $fotoNueva ?: $fotoActual;

        $usuarioDAO->actualizarPerfil((int) $usuario['id_usuario'], $nombre, $fotoPerfil);
        $_SESSION['usuario'] = $usuarioDAO->obtenerPorId((int) $usuario['id_usuario']);
        $usuario = $_SESSION['usuario'];
        $mensaje = 'Perfil actualizado correctamente.';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$nombreActual = $usuario['nombre'] ?? 'Usuario';
$fotoActual = $usuario['foto_perfil'] ?? '';
$inicial = strtoupper(substr($nombreActual, 0, 1));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - SpotCloud</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <?php include __DIR__ . '/partials/nav.php'; ?>

    <main class="contenedor angosto">
        <section class="titulo-pagina">
            <p class="etiqueta">Cuenta</p>
            <h1>Mi perfil</h1>
        </section>

        <?php if ($mensaje): ?>
            <p class="alerta ok"><?= limpiar($mensaje) ?></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="alerta error"><?= limpiar($error) ?></p>
        <?php endif; ?>

        <form class="panel formulario perfil-form" method="POST" enctype="multipart/form-data">
            <div class="perfil-resumen">
                <?php if ($fotoActual): ?>
                    <img class="avatar-grande" src="<?= limpiar($fotoActual) ?>" alt="Foto de perfil">
                <?php else: ?>
                    <span class="avatar-grande avatar-letra"><?= limpiar($inicial) ?></span>
                <?php endif; ?>
                <div>
                    <h2><?= limpiar($nombreActual) ?></h2>
                    <p class="texto-suave"><?= limpiar($usuario['correo'] ?? '') ?></p>
                </div>
            </div>

            <label>Nombre</label>
            <input type="text" name="nombre" value="<?= limpiar($nombreActual) ?>" required>

            <label>Foto de perfil</label>
            <label class="selector-archivo">
                <input class="input-archivo" type="file" name="foto_perfil" accept="image/jpeg,image/png,image/webp" data-texto-archivo="texto-foto-perfil">
                <span>Seleccionar foto</span>
            </label>
            <small class="texto-archivo" id="texto-foto-perfil">Sin archivos seleccionados.</small>
            <small class="ayuda-campo">Si no subis una foto, se muestra la inicial de tu nombre en mayuscula.</small>

            <button class="boton boton-principal" type="submit">Guardar perfil</button>
        </form>
    </main>

    <?php include __DIR__ . '/partials/player.php'; ?>
    <script src="js/app.js"></script>
</body>
</html>