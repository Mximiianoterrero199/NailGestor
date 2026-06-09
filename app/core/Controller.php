<?php

class Controller
{
    protected array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../config/config.php';
    }

    protected function vista(string $vista, array $datos = []): void
    {
        extract($datos);

        $config = $this->config;
        $contenido = __DIR__ . '/../views/' . $vista . '.php';

        require __DIR__ . '/../views/layout.php';
    }

    protected function redirigir(string $ruta): void
    {
        header('Location: ' . $this->config['base_url'] . '/' . ltrim($ruta, '/'));
        exit;
    }
}
