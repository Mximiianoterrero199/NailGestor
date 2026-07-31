<?php

require_once __DIR__ . '/../config/notificaciones.php';

class NotificacionWhatsApp
{
    /**
     * Envía un mensaje de WhatsApp a la dueña via CallMeBot API
     *
     * @param string $mensaje Texto del mensaje a enviar
     * @return bool true si se envió correctamente, false en caso de error
     */
    public static function enviarADuena(string $mensaje): bool
    {
        if (CALLMEBOT_API_KEY === 'TU_API_KEY_AQUI') {
            error_log('[NailGestor] CallMeBot API Key no configurada. Mensaje no enviado.');
            return false;
        }

        $url = 'https://api.callmebot.com/whatsapp.php?'
            . 'phone=' . urlencode(DUENA_TELEFONO)
            . '&text=' . urlencode($mensaje)
            . '&apikey=' . urlencode(CALLMEBOT_API_KEY);

        $contexto = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'timeout' => 10,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $respuesta = @file_get_contents($url, false, $contexto);

        if ($respuesta === false) {
            error_log('[NailGestor] Error al contactar la API de CallMeBot.');
            return false;
        }

        // CallMeBot responde con "Message queued." o "OK" en caso de éxito
        $exito = stripos($respuesta, 'queued') !== false || stripos($respuesta, 'OK') !== false;

        if (!$exito) {
            error_log('[NailGestor] CallMeBot respondió: ' . $respuesta);
        }

        return $exito;
    }
}
