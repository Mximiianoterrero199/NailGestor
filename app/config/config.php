<?php

$env = require __DIR__ . '/../../env.php';

return [
    'app_nombre' => 'NailGestor',
    // Determina la URL base de forma dinámica para que funcione local y en InfinityFree
    'base_url'   => str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']),
    
    // Configuración para envíos de Email reales (Gmail)
    'smtp' => [
        'host'       => $env['SMTP_HOST'],
        'puerto'     => $env['SMTP_PORT'],
        'usuario'    => $env['SMTP_USER'],
        // Debes generar una "Contraseña de aplicación" en tu cuenta de Google
        // NO utilizar la contraseña normal de Gmail.
        'clave'      => $env['SMTP_PASS'], 
    ],
];
