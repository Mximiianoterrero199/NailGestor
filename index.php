<?php

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/controllers/PublicController.php';
require_once __DIR__ . '/app/controllers/AdminController.php';

$controlador = $_GET['controlador'] ?? 'public';
$accion = $_GET['accion'] ?? 'inicio';

$rutas = [
    'public' => PublicController::class,
    'admin' => AdminController::class,
];

if (!isset($rutas[$controlador])) {
    http_response_code(404);
    echo 'Página no encontrada';
    exit;
}

$claseControlador = $rutas[$controlador];
$instancia = new $claseControlador();

if (!method_exists($instancia, $accion)) {
    http_response_code(404);
    echo 'Página no encontrada';
    exit;
}

$instancia->$accion();
