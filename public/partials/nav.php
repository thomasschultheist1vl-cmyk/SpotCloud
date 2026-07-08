<?php
$usuarioNav = usuarioActual();
$nombreNav = $usuarioNav['nombre'] ?? 'Usuario';
$fotoNav = $usuarioNav['foto_perfil'] ?? '';
$inicialNav = strtoupper(substr($nombreNav, 0, 1));
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
        <a class="perfil-link" href="perfil.php" title="Editar perfil">
            <?php if ($fotoNav): ?>
                <img class="avatar" src="<?= limpiar($fotoNav) ?>" alt="Foto de perfil">
            <?php else: ?>
                <span class="avatar"><?= limpiar($inicialNav) ?></span>
            <?php endif; ?>
            <span><?= limpiar($nombreNav) ?></span>
        </a>
        <a href="logout.php">Salir</a>
    </div>
</header>
