<?php
// Registro de usuarios nuevos.
require_once __DIR__ . '/../app/helpers/funciones.php';
require_once __DIR__ . '/../app/dao/UsuarioDAO.php';

iniciarSesion();

$error = '';
$ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibimos y limpiamos los datos enviados por el formulario.
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nombre === '' || $correo === '' || $password === '') {
        $error = 'Completa todos los campos.';
    } else {
        $usuarioDAO = new UsuarioDAO();

        // El correo es unico, por eso se revisa antes de registrar.
        if ($usuarioDAO->obtenerPorCorreo($correo)) {
            $error = 'Ya existe un usuario con ese correo.';
        } else {
            $usuarioDAO->registrar($nombre, $correo, $password);
            $ok = 'Usuario creado. Ya podes iniciar sesion.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpotCloud - Registro</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="cuerpo-auth">
    <main class="caja-auth">
        <div class="marca">
            <span class="icono-marca">S</span>
            <span>SpotCloud</span>
        </div>

        <nav class="tabs-auth">
            <a href="login.php">Iniciar sesion</a>
            <a class="activo" href="registro.php">Registrarse</a>
        </nav>

        <?php if ($error): ?>
            <p class="alerta error"><?= limpiar($error) ?></p>
        <?php endif; ?>

        <?php if ($ok): ?>
            <p class="alerta ok"><?= limpiar($ok) ?></p>
        <?php endif; ?>

        <form method="POST" class="formulario">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" placeholder="Tu nombre" required>

            <label for="correo">Correo electronico</label>
            <input type="email" id="correo" name="correo" placeholder="tu@email.com" required>

            <label for="password">Contrasena</label>
            <input type="password" id="password" name="password" placeholder="Minimo 4 caracteres" required>

            <button class="boton boton-principal" type="submit">Crear cuenta</button>
        </form>
    </main>
</body>
</html>


