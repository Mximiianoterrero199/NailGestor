<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Seguridad.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Turno.php';
require_once __DIR__ . '/../models/HorarioDisponible.php';
require_once __DIR__ . '/../models/Trabajo.php';
require_once __DIR__ . '/../../vendor/autoload.php';

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

        // Validar CSRF
        Seguridad::validarToken();

        $usuario = Seguridad::limpiarString($_POST['usuario'] ?? '', 60);
        $clave   = trim($_POST['clave'] ?? ''); // la clave NO se sanea para not truncar caracteres especiales

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
        $this->proteger();

        $this->vista('admin/registro', [
            'error'   => $_GET['error']   ?? null,
            'success' => $_GET['success'] ?? null,
        ]);
    }

    public function registrar(): void
    {
        $this->proteger();

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

        $this->redirigir('?controlador=admin&accion=turnos&mensaje=' . urlencode('Nuevo administrador creado correctamente.'));
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
            'error'   => $_GET['error']   ?? null,
        ]);
    }

    public function cambiarEstado(): void
    {
        $this->proteger();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?controlador=admin&accion=turnos');
        }

        // Validar CSRF
        Seguridad::validarToken();

        $id     = Seguridad::enteroPositivo($_POST['id'] ?? 0) ?? 0;
        $estado = Seguridad::limpiarString($_POST['estado'] ?? '', 20);

        $estadosPermitidos = ['pendiente', 'confirmado', 'cancelado'];
        if (!in_array($estado, $estadosPermitidos, true)) {
            $this->redirigir('?controlador=admin&accion=turnos&error=' . urlencode('Estado no válido.'));
            return;
        }
        if ($id > 0 && $estado !== '') {
            $turnoModelo = new Turno();
            $cambiado = $turnoModelo->cambiarEstado($id, $estado);

            if ($cambiado) {
                $turnoActualizado = $turnoModelo->buscarConDetalles($id);
                if ($turnoActualizado) {
                    // Preparar mensaje de WhatsApp
                    $tel = preg_replace('/[^0-9]/', '', $turnoActualizado['telefono']);
                    if (substr($tel, 0, 2) !== '54') {
                        // Si le falta el 54 de Argentina (10 digitos asume Cod Area + Num)
                        if (strlen($tel) == 10) {
                            $tel = '549' . $tel; 
                        } else {
                            $tel = '54' . $tel;
                        }
                    }
                    
                    $cliente = trim($turnoActualizado['cliente_nombre']);
                    $servicio = $turnoActualizado['servicio_nombre'];
                    $fechaHora = date('d/m/Y H:i', strtotime($turnoActualizado['fecha_hora']));
                    
                    $estadoTxt = $estado === 'aceptado' ? 'CONFIRMADO ✅' : ($estado === 'cancelado' ? 'CANCELADO ❌' : strtoupper($estado));
                    $textoWa = "Hola $cliente, te escribimos de BrisaNails 💅.\nQuería avisarte que tu turno para *$servicio* el *$fechaHora* ha sido *$estadoTxt*.\n¡Cualquier consulta no dudes en escribirnos!";
                    $waLink = "https://api.whatsapp.com/send?phone=$tel&text=" . rawurlencode($textoWa);
                    
                    $this->redirigir('?controlador=admin&accion=turnos&mensaje=' . urlencode('El estado del turno fue actualizado.') . '&wa_link=' . urlencode($waLink));
                    return;
                }
            }
        }
        $this->redirigir('?controlador=admin&accion=turnos&mensaje=' . urlencode('El estado del turno fue actualizado.'));
    }

    public function eliminar(): void
    {
        $this->proteger();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?controlador=admin&accion=turnos');
        }

        // Validar CSRF
        Seguridad::validarToken();

        $id = Seguridad::enteroPositivo($_POST['id'] ?? 0) ?? 0;

        if ($id > 0) {
            $turnoModelo = new Turno();
            $turnoModelo->eliminar($id);
        }

        $this->redirigir('?controlador=admin&accion=turnos&mensaje=' . urlencode('El turno fue eliminado correctamente.'));
    }

    public function editarTurno(): void
    {
        $this->proteger();

        $id = Seguridad::enteroPositivo($_GET['id'] ?? 0);
        if (!$id) {
            $this->redirigir('?controlador=admin&accion=turnos&error=' . urlencode('Turno no válido.'));
            return;
        }

        $turnoModelo = new Turno();
        $turno = $turnoModelo->buscarConDetalles($id);

        if (!$turno) {
            $this->redirigir('?controlador=admin&accion=turnos&error=' . urlencode('Turno no encontrado.'));
            return;
        }

        $this->vista('admin/editar_turno', [
            'turno' => $turno,
            'error' => $_GET['error'] ?? null,
        ]);
    }

    public function guardarEdicionTurno(): void
    {
        $this->proteger();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?controlador=admin&accion=turnos');
        }

        Seguridad::validarToken();

        $id = Seguridad::enteroPositivo($_POST['id'] ?? 0);
        $fecha = Seguridad::limpiarString($_POST['fecha'] ?? '', 10);
        $horaInicio = Seguridad::limpiarString($_POST['hora_inicio'] ?? '', 5);
        $horaFin = Seguridad::limpiarString($_POST['hora_fin'] ?? '', 5);

        if (!$id || !$fecha || !$horaInicio || !$horaFin) {
            $this->redirigir("?controlador=admin&accion=editarTurno&id=$id&error=" . urlencode('Completá todos los campos de fecha y horario.'));
            return;
        }

        if (!Seguridad::fechaValida($fecha) || !Seguridad::horaValida($horaInicio) || !Seguridad::horaValida($horaFin)) {
            $this->redirigir("?controlador=admin&accion=editarTurno&id=$id&error=" . urlencode('Formato de fecha u hora no válido.'));
            return;
        }

        $fechaHoraInicio = "$fecha $horaInicio:00";
        $fechaHoraFin = "$fecha $horaFin:00";

        if (strtotime($fechaHoraFin) <= strtotime($fechaHoraInicio)) {
            $this->redirigir("?controlador=admin&accion=editarTurno&id=$id&error=" . urlencode('La hora de fin debe ser posterior a la de inicio.'));
            return;
        }

        $turnoModelo = new Turno();
        $turnoModelo->actualizarHorarios($id, $fechaHoraInicio, $fechaHoraFin);

        $this->redirigir('?controlador=admin&accion=turnos&mensaje=' . urlencode('Horarios del turno actualizados correctamente.'));
    }

    // ---------------------------------------------------------------
    // Gestión de Horarios Disponibles
    // ---------------------------------------------------------------

    public function horarios(): void
    {
        $this->proteger();

        $horarioModelo = new HorarioDisponible();

        $this->vista('admin/horarios', [
            'horarios' => $horarioModelo->todos(),
            'mensaje'  => $_GET['mensaje'] ?? null,
            'error'    => $_GET['error']   ?? null,
        ]);
    }

    public function guardarHorarios(): void
    {
        $this->proteger();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?controlador=admin&accion=horarios');
        }

        // Validar CSRF
        Seguridad::validarToken();

        $diasNombres = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $horarios = [];

        for ($dia = 0; $dia <= 6; $dia++) {
            $activo     = isset($_POST["activo_$dia"]) ? 1 : 0;
            // Validar formato HH:MM de inicio y fin
            $horaInicio = Seguridad::limpiarString($_POST["inicio_$dia"] ?? '09:00', 5);
            $horaFin    = Seguridad::limpiarString($_POST["fin_$dia"]    ?? '19:00', 5);

            if (!Seguridad::horaValida($horaInicio)) { $horaInicio = '09:00'; }
            if (!Seguridad::horaValida($horaFin))    { $horaFin    = '19:00'; }

            $horarios[] = [
                'dia_semana'  => $dia,
                'hora_inicio' => $horaInicio,
                'hora_fin'    => $horaFin,
                'activo'      => $activo,
            ];
        }

        $horarioModelo = new HorarioDisponible();
        $horarioModelo->guardarTodos($horarios);

        $this->redirigir('?controlador=admin&accion=horarios&mensaje=' . urlencode('Horarios actualizados correctamente.'));
    }

    // ---------------------------------------------------------------
    // Gestión de Mis Trabajos (Galería)
    // ---------------------------------------------------------------

    public function trabajos(): void
    {
        $this->proteger();

        $trabajoModelo = new Trabajo();

        $this->vista('admin/trabajos', [
            'trabajos' => $trabajoModelo->todos(),
            'mensaje'  => $_GET['mensaje'] ?? null,
            'error'    => $_GET['error']   ?? null,
        ]);
    }

    public function guardarTrabajo(): void
    {
        $this->proteger();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?controlador=admin&accion=trabajos');
        }

        Seguridad::validarToken();

        $titulo = Seguridad::limpiarString($_POST['titulo'] ?? '', 100);
        $imagen = $_FILES['imagen'] ?? null;

        if (!$imagen || $imagen['error'] !== UPLOAD_ERR_OK) {
            $this->redirigir('?controlador=admin&accion=trabajos&error=' . urlencode('Error al subir la imagen. Asegurate de seleccionar un archivo.'));
            return;
        }

        // Validar tipo real con finfo (más seguro que ['type'])
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipoReal = finfo_file($finfo, $imagen['tmp_name']);
        finfo_close($finfo);

        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($tipoReal, $tiposPermitidos)) {
            $this->redirigir('?controlador=admin&accion=trabajos&error=' . urlencode('Formato no permitido. Usá JPG, PNG o WEBP.'));
            return;
        }

        // Generar nombre único pero forzando extensión .webp
        $nombreImagen = uniqid('trabajo_', true) . '.webp';
        
        $baseDir = dirname(dirname(__DIR__)); 
        $directorioDestino = $baseDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'trabajos';
        
        if (!is_dir($directorioDestino)) {
            if (!mkdir($directorioDestino, 0755, true)) {
                $this->redirigir('?controlador=admin&accion=trabajos&error=' . urlencode('No se pudo crear la carpeta de destino en el servidor.'));
                return;
            }
        }
        
        $rutaDestino = rtrim($directorioDestino, '/\\') . DIRECTORY_SEPARATOR . $nombreImagen;

        // Procesar imagen con GD: Redimensionar y convertir a WebP
        $info = getimagesize($imagen['tmp_name']);
        if ($info === false) {
            $this->redirigir('?controlador=admin&accion=trabajos&error=' . urlencode('El archivo no es una imagen válida.'));
            return;
        }

        $anchoOriginal = $info[0];
        $altoOriginal  = $info[1];

        // tipoMime ya validado via finfo = tipoReal
        $tipoMime = $tipoReal;

        // Límite de resolución: 1000px
        $maxResolucion = 1000;
        $ratio = min($maxResolucion / $anchoOriginal, $maxResolucion / $altoOriginal);
        $nuevoAncho = $anchoOriginal;
        $nuevoAlto = $altoOriginal;

        if ($ratio < 1) {
            $nuevoAncho = (int)($anchoOriginal * $ratio);
            $nuevoAlto = (int)($altoOriginal * $ratio);
        }

        $imagenProcesada = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

        // Mantener transparencia si aplica
        imagealphablending($imagenProcesada, false);
        imagesavealpha($imagenProcesada, true);
        $colorTransparente = imagecolorallocatealpha($imagenProcesada, 255, 255, 255, 127);
        imagefilledrectangle($imagenProcesada, 0, 0, $nuevoAncho, $nuevoAlto, $colorTransparente);

        $imagenOriginal = null;
        switch ($tipoMime) {
            case 'image/jpeg':
                $imagenOriginal = imagecreatefromjpeg($imagen['tmp_name']);
                break;
            case 'image/png':
                $imagenOriginal = imagecreatefrompng($imagen['tmp_name']);
                break;
            case 'image/webp':
                $imagenOriginal = imagecreatefromwebp($imagen['tmp_name']);
                break;
            default:
                $this->redirigir('?controlador=admin&accion=trabajos&error=' . urlencode('Formato de origen no soportado.'));
                return;
        }
        if ($imagenOriginal === null || $imagenOriginal === false) {
            $this->redirigir('?controlador=admin&accion=trabajos&error=' . urlencode('No se pudo leer la imagen. Intentá con otro archivo.'));
            return;
        }

        imagecopyresampled($imagenProcesada, $imagenOriginal, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $anchoOriginal, $altoOriginal);

        // Guardar como WebP al 80% de calidad
        if (imagewebp($imagenProcesada, $rutaDestino, 80)) {
            imagedestroy($imagenProcesada);
            imagedestroy($imagenOriginal);

            $trabajoModelo = new Trabajo();
            $trabajoModelo->crear($nombreImagen, $titulo !== '' ? $titulo : null);
            
            $this->redirigir('?controlador=admin&accion=trabajos&mensaje=' . urlencode('Imagen comprimida a WebP y subida exitosamente.'));
        } else {
            $this->redirigir('?controlador=admin&accion=trabajos&error=' . urlencode('Error al convertir la imagen a formato WebP.'));
        }
    }

    public function eliminarTrabajo(): void
    {
        $this->proteger();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?controlador=admin&accion=trabajos');
        }

        Seguridad::validarToken();

        $id = Seguridad::enteroPositivo($_POST['id'] ?? 0) ?? 0;

        if ($id > 0) {
            $trabajoModelo = new Trabajo();
            $trabajo = $trabajoModelo->buscar($id);

            if ($trabajo) {
                // Eliminar archivo físico
                $rutaImagen = __DIR__ . '/../../public/uploads/trabajos/' . $trabajo['imagen'];
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
                
                $trabajoModelo->eliminar($id);
                $this->redirigir('?controlador=admin&accion=trabajos&mensaje=' . urlencode('Trabajo eliminado correctamente.'));
                return;
            }
        }

        $this->redirigir('?controlador=admin&accion=trabajos&error=' . urlencode('No se pudo eliminar el trabajo.'));
    }

    public function actualizarTrabajo(): void
    {
        $this->proteger();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?controlador=admin&accion=trabajos');
        }

        Seguridad::validarToken();

        $id = Seguridad::enteroPositivo($_POST['id'] ?? 0) ?? 0;
        $titulo = Seguridad::limpiarString($_POST['titulo'] ?? '', 100);

        if ($id > 0) {
            $trabajoModelo = new Trabajo();
            $trabajo = $trabajoModelo->buscar($id);

            if ($trabajo) {
                $trabajoModelo->actualizarTitulo($id, $titulo !== '' ? $titulo : null);
                $this->redirigir('?controlador=admin&accion=trabajos&mensaje=' . urlencode('Descripción actualizada correctamente.'));
                return;
            }
        }

        $this->redirigir('?controlador=admin&accion=trabajos&error=' . urlencode('No se pudo actualizar la descripción original.'));
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
}
