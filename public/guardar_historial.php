<?php
require_once __DIR__ . '/../app/helpers/funciones.php';
require_once __DIR__ . '/../app/dao/HistorialDAO.php';

header('Content-Type: application/json');
iniciarSesion();

if (!estaLogueado()) {
    echo json_encode(['ok' => false, 'mensaje' => 'Usuario no logueado']);
    exit;
}

$idCancion = (int) ($_POST['id_cancion'] ?? 0);

if ($idCancion <= 0) {
    echo json_encode(['ok' => false, 'mensaje' => 'Cancion invalida']);
    exit;
}

$usuario = usuarioActual();
$historialDAO = new HistorialDAO();
$historialDAO->guardar((int) $usuario['id_usuario'], $idCancion);

echo json_encode(['ok' => true]);


