<?php

return [
    'app_nombre' => 'NailGestor',
    // Determina la URL base de forma dinámica para que funcione local y en InfinityFree
    'base_url'   => str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']),
    
    // Configuración para envíos de Email reales (Gmail)
    'smtp' => [
        'host'       => 'smtp.gmail.com',
        'puerto'     => 465,
        'usuario'    => 'tu_correo@gmail.com',
        // Debes generar una "Contraseña de aplicación" en tu cuenta de Google
        // NO utilizar la contraseña normal de Gmail.
        'clave'      => 'TU_CONTRASEÑA_DE_APLICACION_DE_GMAIL', 
    ],
];
