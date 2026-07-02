<?php

// Inicia la sesion solo si todavia no existe una activa.
function iniciarSesion(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Indica si hay un usuario guardado en la sesion.
function estaLogueado(): bool
{
    iniciarSesion();
    return isset($_SESSION['usuario']);
}

// Devuelve los datos del usuario conectado o null si no hay login.
function usuarioActual(): ?array
{
    iniciarSesion();
    return $_SESSION['usuario'] ?? null;
}

// Protege paginas privadas. Si no hay usuario, manda al login.
function protegerPagina(): void
{
    if (!estaLogueado()) {
        header('Location: login.php');
        exit;
    }
}

// Evita que texto de la base de datos rompa el HTML o inyecte codigo.
function limpiar(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

// Convierte el TIME de MySQL a un formato mas natural para canciones.
function formatearDuracion(?string $duracion): string
{
    if (!$duracion) {
        return '00:00';
    }

    $partes = explode(':', $duracion);
    if (count($partes) !== 3) {
        return $duracion;
    }

    $primerNumero = (int) $partes[0];
    $segundoNumero = (int) $partes[1];
    $tercerNumero = (int) $partes[2];

    if ($primerNumero > 0 && $tercerNumero === 0) {
        return sprintf('%02d:%02d', $primerNumero, $segundoNumero);
    }

    if ($primerNumero === 0) {
        return sprintf('%02d:%02d', $segundoNumero, $tercerNumero);
    }

    return sprintf('%02d:%02d:%02d', $primerNumero, $segundoNumero, $tercerNumero);
}

// Indica si una cancion viene de una URL externa o de una carpeta local del proyecto.
function obtenerOrigenCancion(?string $ruta): array
{
    $ruta = trim((string) $ruta);
    $esNube = (bool) preg_match('/^https?:\/\//i', $ruta);

    if ($esNube) {
        return [
            'texto' => 'Nube',
            'clase' => 'origen-nube',
            'titulo' => 'Archivo alojado en la nube',
        ];
    }

    return [
        'texto' => 'Local',
        'clase' => 'origen-local',
        'titulo' => 'Archivo guardado en public/uploads/canciones',
    ];
}
