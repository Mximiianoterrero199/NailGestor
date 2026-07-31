<?php

/**
 * Configuración de notificaciones WhatsApp via CallMeBot
 *
 * INSTRUCCIONES DE CONFIGURACIÓN (solo una vez):
 * 1. Agregá el contacto +34 644 63 69 13 en tu WhatsApp con el nombre "CallMeBot"
 * 2. Enviá este mensaje desde el WhatsApp de la dueña (2612302153):
 *    "I allow callmebot to send me messages"
 * 3. Recibirás un mensaje con tu apikey personal (ej: 1234567)
 * 4. Reemplazá el valor de CALLMEBOT_API_KEY con esa clave
 *
 * Guia oficial: https://www.callmebot.com/blog/free-api-whatsapp-messages/
 */

$env = require __DIR__ . '/../../env.php';

define('DUENA_TELEFONO', '5492612302153'); // Número con código de país (54 Argentina, 9, sin 0 ni 15)
define('CALLMEBOT_API_KEY', $env['CALLMEBOT_API_KEY']); // Reemplazá con tu apikey de CallMeBot
