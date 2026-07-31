<?php

session_start();

// ------------------------------------------------------------------
// Seguridad: cargar clase y aplicar headers HTTP de protección
// ------------------------------------------------------------------
require_once __DIR__ . '/app/core/Seguridad.php';
Seguridad::aplicarHeaders();

// ------------------------------------------------------------------
// Mostrar errores solo en desarrollo local (LAMPP).
// En producción se recomienda poner ini_set('display_errors', 0).
// ------------------------------------------------------------------
$esLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1'], true);
if ($esLocal) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/controllers/PublicController.php';
require_once __DIR__ . '/app/controllers/AdminController.php';

// ------------------------------------------------------------------
// Enrutamiento simple: whitelist de controladores y acciones
// ------------------------------------------------------------------
$controladorNombre = $_GET['controlador'] ?? 'public';
$accion            = $_GET['accion']      ?? 'inicio';

// Whitelist de rutas permitidas (evita exposición de métodos internos)
$rutasPermitidas = [
    'public' => [
        'inicio', 'reservar_form', 'reservar', 'apiHorarios',
    ],
    'admin' => [
        'login', 'ingresar', 'registro', 'registrar',
        'turnos', 'cambiarEstado', 'eliminar',
        'horarios', 'guardarHorarios', 'trabajos', 
        'guardarTrabajo', 'eliminarTrabajo', 'actualizarTrabajo', 'salir',
    ],
];

$claseControlador = [
    'public' => PublicController::class,
    'admin'  => AdminController::class,
];

// Validar que el controlador y la acción estén en la whitelist
if (
    !isset($rutasPermitidas[$controladorNombre]) ||
    !in_array($accion, $rutasPermitidas[$controladorNombre], true)
) {
    http_response_code(404);
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Página no encontrada</title></head>'
       . '<body><h1>404 — Página no encontrada</h1><p><a href="/">Volver al inicio</a></p></body></html>';
    exit;
}

// Sanitizar acción para que no contenga nada raro antes de llamarla
$accion = htmlspecialchars($accion, ENT_QUOTES, 'UTF-8');

$instancia = new $claseControlador[$controladorNombre]();
$instancia->$accion();
