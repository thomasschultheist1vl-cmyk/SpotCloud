<?php
$usuarioNav = usuarioActual();
?>
<header class="barra-superior">
    <a class="marca" href="index.php">
        <span class="icono-marca">S</span>
        <span>SpotCloud</span>
    </a>

    <nav class="menu-principal">
        <a href="index.php">Inicio</a>
        <a href="buscar.php">Buscar</a>
        <a href="admin.php">Cargar</a>
    </nav>

    <div class="menu-usuario">
        <span class="avatar"><?= limpiar(substr($usuarioNav['nombre'] ?? 'U', 0, 1)) ?></span>
        <span><?= limpiar($usuarioNav['nombre'] ?? 'Usuario') ?></span>
        <a href="logout.php">Salir</a>
    </div>
</header>


