<?php
// Endpoint usado por JavaScript para agregar o quitar canciones de la fila.
require_once __DIR__ . '/../app/helpers/funciones.php';
require_once __DIR__ . '/../app/dao/ColaReproduccionDAO.php';

protegerPagina();
header('Content-Type: application/json; charset=utf-8');

$usuario = usuarioActual();
$colaDAO = new ColaReproduccionDAO();
$accion = $_POST['accion'] ?? '';
$idCancion = (int) ($_POST['id_cancion'] ?? 0);
$idUsuario = (int) $usuario['id_usuario'];

try {
    if ($accion === 'agregar') {
        $resultado = $colaDAO->agregarCancion($idUsuario, $idCancion);
        echo json_encode($resultado);
        exit;
    }

    if ($accion === 'quitar') {
        $quitada = $colaDAO->quitarCancion($idUsuario, $idCancion);
        echo json_encode([
            'ok' => true,
            'quitada' => $quitada,
            'mensaje' => $quitada ? 'Cancion quitada de tu fila.' : 'La cancion no estaba en tu fila.',
            'canciones' => $colaDAO->obtenerCancionesPorUsuario($idUsuario),
        ]);
        exit;
    }

    if ($accion === 'limpiar') {
        $cantidad = $colaDAO->limpiarPorUsuario($idUsuario);
        echo json_encode([
            'ok' => true,
            'cantidad' => $cantidad,
            'mensaje' => 'Fila vaciada.',
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['ok' => false, 'mensaje' => 'Accion no valida.']);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensaje' => 'No se pudo actualizar la fila.']);
}