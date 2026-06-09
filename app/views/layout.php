<?php
$titulo = $titulo ?? $config['app_nombre'];
$esAdmin = isset($esAdmin) ? $esAdmin : false;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="NailGestor — Sistema profesional de gestión de turnos y servicios de manicura.">
    <title><?= htmlspecialchars($titulo) ?></title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💅</text></svg>">
    <link rel="stylesheet" href="<?= htmlspecialchars($config['base_url']) ?>/public/css/estilos.css">
</head>
<body>
    <header class="encabezado">
        <a class="marca" href="<?= htmlspecialchars($config['base_url']) ?>/">NailGestor</a>
        <nav class="navegacion">
            <a href="<?= htmlspecialchars($config['base_url']) ?>/">Servicios</a>
            <?php if (!empty($_SESSION['admin_autenticado'])): ?>
                <a class="nav-admin" href="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=turnos">Panel Admin</a>
            <?php else: ?>
                <a class="nav-admin" href="<?= htmlspecialchars($config['base_url']) ?>/?controlador=admin&accion=login">Admin</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="contenedor">
        <?php require $contenido; ?>
    </main>

    <footer class="pie">
        <span>NailGestor</span> &mdash; Gestión profesional de turnos &copy; <?= date('Y') ?>
    </footer>
</body>
</html>
