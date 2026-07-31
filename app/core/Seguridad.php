<?php

/**
 * Clase de seguridad centralizada para NailGestor.
 * Maneja: CSRF tokens, sanitización de entradas y headers HTTP seguros.
 */
class Seguridad
{
    // ---------------------------------------------------------------
    // Headers HTTP de seguridad
    // ---------------------------------------------------------------

    /**
     * Aplica los headers HTTP de seguridad recomendados.
     * Llamar al inicio del request, antes de cualquier output.
     */
    public static function aplicarHeaders(): void
    {
        // Evitar que el navegador "adivine" el content-type
        header('X-Content-Type-Options: nosniff');

        // Protección clickjacking: solo permite iframes del mismo origen
        header('X-Frame-Options: SAMEORIGIN');

        // Activar XSS filter en navegadores legacy
        header('X-XSS-Protection: 1; mode=block');

        // No enviar el Referer a sitios externos
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // Content Security Policy: permitir fuentes propias, Google Fonts, y recursos de n8n (CDN y Webhook)
        header("Content-Security-Policy: default-src 'self'; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://unpkg.com https://cdn.jsdelivr.net; " .
               "font-src 'self' https://fonts.gstatic.com; " .
               "script-src 'self' 'unsafe-inline' https://unpkg.com https://cdn.jsdelivr.net; " .
               "img-src 'self' data: https:; " .
               "connect-src 'self' https://*.app.n8n.cloud;");
    }

    // ---------------------------------------------------------------
    // CSRF — Cross-Site Request Forgery
    // ---------------------------------------------------------------

    /**
     * Genera (o reutiliza) el token CSRF de la sesión actual.
     */
    public static function generarToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Valida que el token enviado en el POST coincida con el de sesión.
     * Si falla, termina la ejecución con 403.
     */
    public static function validarToken(): void
    {
        $tokenEnviado = $_POST['csrf_token'] ?? '';
        $tokenSesion  = $_SESSION['csrf_token'] ?? '';

        if (
            empty($tokenEnviado) ||
            empty($tokenSesion) ||
            !hash_equals($tokenSesion, $tokenEnviado)
        ) {
            http_response_code(403);
            exit('Solicitud no válida. Por favor recargá la página e intentá de nuevo.');
        }

        // Rotar token después de validarlo (previene replay attacks)
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    /**
     * Retorna el campo <input> oculto con el token CSRF listo para usar en formularios.
     */
    public static function campoToken(): string
    {
        $token = self::generarToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    // ---------------------------------------------------------------
    // Sanitización y validación de entradas
    // ---------------------------------------------------------------

    /**
     * Limpia un string: trim + elimina tags HTML.
     */
    public static function limpiarString(string $valor, int $maxLen = 255): string
    {
        $valor = trim($valor);
        $valor = strip_tags($valor);
        $valor = mb_substr($valor, 0, $maxLen, 'UTF-8');
        return $valor;
    }

    /**
     * Valida y retorna un entero positivo, o null si es inválido.
     */
    public static function enteroPositivo(mixed $valor): ?int
    {
        $int = filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        return $int !== false ? (int) $int : null;
    }

    /**
     * Valida formato de fecha YYYY-MM-DD.
     * Retorna true si es válida y no es del pasado.
     */
    public static function fechaValida(string $fecha): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return false;
        }
        $d = DateTime::createFromFormat('Y-m-d', $fecha);
        if (!$d || $d->format('Y-m-d') !== $fecha) {
            return false;
        }
        // No permitir fechas en el pasado (comparar solo fecha, no hora)
        $hoy = new DateTime('today');
        return $d >= $hoy;
    }

    /**
     * Valida formato de hora HH:MM.
     */
    public static function horaValida(string $hora): bool
    {
        return (bool) preg_match('/^\d{2}:\d{2}$/', $hora) &&
               preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $hora);
    }

    /**
     * Valida un email (puede ser vacío — es opcional).
     */
    public static function emailValido(string $email): bool
    {
        if ($email === '') {
            return true; // email es campo opcional
        }
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Valida que un teléfono solo contenga números, espacios, guiones y +.
     */
    public static function telefonoValido(string $tel): bool
    {
        return (bool) preg_match('/^[\d\s\+\-\(\)]{6,20}$/', $tel);
    }
}
