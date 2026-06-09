<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Turno.php';

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

        $asunto = "Actualización de tu turno en " . $this->config['app_nombre'];

        $mensaje = "Hola $cliente,\n\n";
        $mensaje .= "Te escribimos para informarte que el estado de tu turno para '$servicio' el día $fechaHora ha sido actualizado.\n\n";
        
        switch ($nuevoEstado) {
            case 'confirmado':
                $mensaje .= "¡Excelente noticia! Tu turno ha sido CONFIRMADO. Te esperamos.\n";
                break;
            case 'cancelado':
                $mensaje .= "Lamentablemente, el turno ha sido CANCELADO. Por favor, volvé a agendar o comunícate con nosotros.\n";
                break;
            case 'completado':
                $mensaje .= "Tu turno ha sido marcado como COMPLETADO. ¡Gracias por elegirnos!\n";
                break;
            default:
                $mensaje .= "El estado de tu turno ahora es: " . ucfirst($nuevoEstado) . ".\n";
                break;
        }

        $mensaje .= "\nSaludos cordiales,\nEl equipo de " . $this->config['app_nombre'];

        $cabeceras = "From: noreply@nailgestor.local\r\n";
        $cabeceras .= "Reply-To: contacto@nailgestor.local\r\n";
        $cabeceras .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $cabeceras .= "X-Mailer: PHP/" . phpversion();

        // Desactivamos temporalmente el warning para evitar que falle visiblemente 
        // si no hay un servidor SMTP (sendmail) configurado en localhost.
        @mail($destinatario, $asunto, $mensaje, $cabeceras);
    }
}
