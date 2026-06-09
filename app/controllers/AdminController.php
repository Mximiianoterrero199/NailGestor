<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Turno.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AdminController extends Controller
{
    // ---------------------------------------------------------------
    // Login
    // ---------------------------------------------------------------

    public function login(): void
    {
        if ($this->estaAutenticado()) {
            $this->redirigir('?controlador=admin&accion=turnos');
        }

        $this->vista('admin/login', [
            'error' => $_GET['error'] ?? null,
        ]);
    }

    public function ingresar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?controlador=admin&accion=login');
        }

        $usuario = trim($_POST['usuario'] ?? '');
        $clave   = trim($_POST['clave']   ?? '');

        if ($usuario === '' || $clave === '') {
            $this->redirigir('?controlador=admin&accion=login&error=' . urlencode('Completá todos los campos.'));
        }

        $adminModelo = new Admin();
        $admin = $adminModelo->buscarPorUsuario($usuario);

        if ($admin === null || !$adminModelo->verificarClave($clave, $admin['clave'])) {
            $this->redirigir('?controlador=admin&accion=login&error=' . urlencode('Usuario o contraseña incorrectos.'));
        }

        // Credenciales correctas → iniciar sesión
        session_regenerate_id(true);
        $_SESSION['admin_autenticado'] = true;
        $_SESSION['admin_usuario']     = $admin['usuario'];

        $this->redirigir('?controlador=admin&accion=turnos');
    }

    // ---------------------------------------------------------------
    // Registro de nuevo administrador
    // ---------------------------------------------------------------

    public function registro(): void
    {
        if ($this->estaAutenticado()) {
            $this->redirigir('?controlador=admin&accion=turnos');
        }

        $this->vista('admin/registro', [
            'error'   => $_GET['error']   ?? null,
            'success' => $_GET['success'] ?? null,
        ]);
    }

    public function registrar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?controlador=admin&accion=registro');
        }

        $usuario        = trim($_POST['usuario']         ?? '');
        $clave          = trim($_POST['clave']            ?? '');
        $claveConfirmar = trim($_POST['clave_confirmar']  ?? '');

        // Validaciones
        if ($usuario === '' || $clave === '' || $claveConfirmar === '') {
            $this->redirigir('?controlador=admin&accion=registro&error=' . urlencode('Completá todos los campos.'));
        }

        if (strlen($usuario) < 3 || strlen($usuario) > 60) {
            $this->redirigir('?controlador=admin&accion=registro&error=' . urlencode('El usuario debe tener entre 3 y 60 caracteres.'));
        }

        if (strlen($clave) < 8) {
            $this->redirigir('?controlador=admin&accion=registro&error=' . urlencode('La contraseña debe tener al menos 8 caracteres.'));
        }

        if ($clave !== $claveConfirmar) {
            $this->redirigir('?controlador=admin&accion=registro&error=' . urlencode('Las contraseñas no coinciden.'));
        }

        $adminModelo = new Admin();

        if ($adminModelo->existeUsuario($usuario)) {
            $this->redirigir('?controlador=admin&accion=registro&error=' . urlencode('Ese nombre de usuario ya está en uso.'));
        }

        $adminModelo->crear($usuario, $clave);

        $this->redirigir('?controlador=admin&accion=login&success=' . urlencode('Cuenta creada correctamente. Ya podés ingresar.'));
    }

    // ---------------------------------------------------------------
    // Turnos
    // ---------------------------------------------------------------

    public function turnos(): void
    {
        $this->proteger();

        $turnoModelo = new Turno();

        $this->vista('admin/turnos', [
            'turnos'  => $turnoModelo->todosOrdenadosPorFecha(),
            'mensaje' => $_GET['mensaje'] ?? null,
        ]);
    }

    public function cambiarEstado(): void
    {
        $this->proteger();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?controlador=admin&accion=turnos');
        }

        $id     = (int) ($_POST['id']     ?? 0);
        $estado = trim($_POST['estado']    ?? '');

        if ($id > 0 && $estado !== '') {
            $turnoModelo = new Turno();
            $cambiado = $turnoModelo->cambiarEstado($id, $estado);

            if ($cambiado) {
                $turnoActualizado = $turnoModelo->buscarConDetalles($id);
                if ($turnoActualizado && !empty($turnoActualizado['email'])) {
                    $this->enviarNotificacionEmail($turnoActualizado, $estado);
                }
            }
        }

        $this->redirigir('?controlador=admin&accion=turnos&mensaje=' . urlencode('El estado del turno fue actualizado. Se notificó al cliente.'));
    }

    // ---------------------------------------------------------------
    // Cerrar sesión
    // ---------------------------------------------------------------

    public function salir(): void
    {
        session_destroy();
        $this->redirigir('');
    }

    // ---------------------------------------------------------------
    // Helpers privados
    // ---------------------------------------------------------------

    private function proteger(): void
    {
        if (!$this->estaAutenticado()) {
            $this->redirigir('?controlador=admin&accion=login');
        }
    }

    private function estaAutenticado(): bool
    {
        return !empty($_SESSION['admin_autenticado']);
    }

    private function enviarNotificacionEmail(array $turno, string $nuevoEstado): void
    {
        $destinatario = $turno['email'];
        $cliente      = $turno['cliente_nombre'];
        $servicio     = $turno['servicio_nombre'];
        $fechaHora    = date('d/m/Y H:i', strtotime($turno['fecha_hora']));

        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host       = $this->config['smtp']['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->config['smtp']['usuario'];
            $mail->Password   = $this->config['smtp']['clave'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // O usar ENCRYPTION_STARTTLS para el puerto 587
            $mail->Port       = $this->config['smtp']['puerto'];
            $mail->CharSet    = 'UTF-8';

            // Remitente y destinatario
            $mail->setFrom($this->config['smtp']['usuario'], $this->config['app_nombre']);
            $mail->addAddress($destinatario, $cliente);

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = "Actualización de tu turno en " . $this->config['app_nombre'];

            $mensajeHtml = "<h2>Hola $cliente,</h2>";
            $mensajeHtml .= "<p>Te escribimos para informarte que ha habido una actualización en tu turno para <strong>'$servicio'</strong> programado para el día <strong>$fechaHora</strong>.</p>";
            
            switch ($nuevoEstado) {
                case 'confirmado':
                    $mensajeHtml .= "<p style='color: #1f7a4d;'><strong>¡Excelente noticia! Tu turno ha sido CONFIRMADO. Te esperamos.</strong></p>";
                    break;
                case 'cancelado':
                    $mensajeHtml .= "<p style='color: #b43a3a;'><strong>Lamentablemente, el turno ha sido CANCELADO.</strong></p><p>Por favor, volvé a agendar o comunícate con nosotros para más detalles.</p>";
                    break;
                case 'completado':
                    $mensajeHtml .= "<p style='color: #60a5fa;'><strong>Tu turno ha sido marcado como COMPLETADO.</strong></p><p>¡Gracias por elegirnos!</p>";
                    break;
                default:
                    $mensajeHtml .= "<p>El estado de tu turno ahora es: <strong>" . ucfirst($nuevoEstado) . "</strong>.</p>";
                    break;
            }

            $mensajeHtml .= "<br><p>Saludos cordiales,<br>El equipo de <strong>" . $this->config['app_nombre'] . "</strong></p>";

            $mail->Body    = $mensajeHtml;
            $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $mensajeHtml));

            $mail->send();
        } catch (Exception $e) {
            // Log de error opcional aquí: 
            // error_log("Error al enviar correo: {$mail->ErrorInfo}");
        }
    }
}
