<?php
// Login de usuarios. Si el usuario existe, se guarda en $_SESSION.
require_once __DIR__ . '/../app/helpers/funciones.php';
require_once __DIR__ . '/../app/dao/UsuarioDAO.php';

iniciarSesion();

if (estaLogueado()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    $usuarioDAO = new UsuarioDAO();
    $usuario = $usuarioDAO->validarLogin($correo, $password);

    if ($usuario) {
        // Guardamos solo datos basicos en sesion y activamos el splash de bienvenida.
        $_SESSION['usuario'] = $usuario;
        $_SESSION['mostrar_intro'] = true;
        header('Location: index.php');
        exit;
    }

    $error = 'Correo o contrasena incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpotCloud - Login</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="cuerpo-auth">
    <main class="caja-auth">
        <div class="marca">
            <span class="icono-marca">S</span>
            <span>SpotCloud</span>
        </div>

        <nav class="tabs-auth">
            <a class="activo" href="login.php">Iniciar sesion</a>
            <a href="registro.php">Registrarse</a>
        </nav>

        <?php if ($error): ?>
            <p class="alerta error"><?= limpiar($error) ?></p>
        <?php endif; ?>

        <form method="POST" class="formulario">
            <label for="correo">Correo electronico</label>
            <input type="email" id="correo" name="correo" placeholder="tu@email.com" required>

            <label for="password">Contrasena</label>
            <input type="password" id="password" name="password" placeholder="Tu contrasena" required>

            <button class="boton boton-principal" type="submit">Iniciar sesion</button>
        </form>
    </main>
</body>
</html>


