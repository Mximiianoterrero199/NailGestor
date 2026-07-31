<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Seguridad.php';
require_once __DIR__ . '/../core/NotificacionWhatsApp.php';
require_once __DIR__ . '/../models/Servicio.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Turno.php';
require_once __DIR__ . '/../models/HorarioDisponible.php';
require_once __DIR__ . '/../models/Trabajo.php';

class PublicController extends Controller
{
    public function inicio(): void
    {
        $servicioModelo = new Servicio();
        $trabajoModelo  = new Trabajo();

        $this->vista('public/inicio', [
            'servicios' => $servicioModelo->todos(),
            'trabajos'  => $trabajoModelo->todos(),
            'mensaje'   => $_GET['mensaje'] ?? null,
            'error'     => $_GET['error'] ?? null,
        ]);
    }

    public function reservar_form(): void
    {
        $servicioModelo = new Servicio();
        $horarioModelo = new HorarioDisponible();
        
        // Build a map of active days for JS
        $horarios = $horarioModelo->todos();
        $diasActivos = [];
        foreach ($horarios as $h) {
            $diasActivos[$h['dia_semana']] = [
                'activo'      => (bool) $h['activo'],
                'hora_inicio' => $h['hora_inicio'],
                'hora_fin'    => $h['hora_fin'],
            ];
        }

        $this->vista('public/reservar', [
            'servicios'    => $servicioModelo->todos(),
            'diasActivos'  => $diasActivos,
            'error'        => $_GET['error'] ?? null,
        ]);
    }

    public function reservar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('');
        }

        // ── Validar CSRF token ──────────────────────────────────────
        Seguridad::validarToken();

        // ── Sanitizar y validar todos los campos ────────────────────
        $nombre     = Seguridad::limpiarString($_POST['nombre']     ?? '', 100);
        $telefono   = Seguridad::limpiarString($_POST['telefono']   ?? '', 20);
        $email      = Seguridad::limpiarString($_POST['email']      ?? '', 100);
        $servicioId = Seguridad::enteroPositivo($_POST['servicio_id'] ?? 0);
        $fecha      = Seguridad::limpiarString($_POST['fecha']      ?? '', 10);
        $hora       = Seguridad::limpiarString($_POST['hora']       ?? '', 5);

        // Campos obligatorios vacíos
        if ($nombre === '' || $telefono === '' || $fecha === '' || $hora === '' || $servicioId === null) {
            $this->redirigir('?accion=reservar_form&error=Completá los campos obligatorios y seleccioná un horario para reservar el turno.');
        }

        // Validaciones de formato
        if (!Seguridad::telefonoValido($telefono)) {
            $this->redirigir('?accion=reservar_form&error=El número de teléfono ingresado no es válido.');
        }

        if (!Seguridad::emailValido($email)) {
            $this->redirigir('?accion=reservar_form&error=El correo electrónico ingresado no es válido.');
        }

        if (!Seguridad::fechaValida($fecha)) {
            $this->redirigir('?accion=reservar_form&error=La fecha seleccionada no es válida.');
        }

        if (!Seguridad::horaValida($hora)) {
            $this->redirigir('?accion=reservar_form&error=El horario seleccionado no es válido.');
        }

        $servicioModelo = new Servicio();
        $servicio = $servicioModelo->buscar($servicioId);

        if ($servicio === null) {
            $this->redirigir('?accion=reservar_form&error=El servicio seleccionado no existe.');
        }

        $fechaHora = $fecha . ' ' . $hora . ':00';
        
        $duracion = (int) $servicio['duracion_minutos'];
        $fechaHoraFin = date('Y-m-d H:i:s', strtotime("+$duracion minutes", strtotime($fechaHora)));

        $clienteModelo = new Cliente();
        $turnoModelo   = new Turno();

        $clienteId = $clienteModelo->crear($nombre, $telefono, $email === '' ? null : $email);
        $turnoModelo->crear($clienteId, $servicioId, $fechaHora, $fechaHoraFin);

        // Enviar notificación automática por WhatsApp a la dueña
        $fechaFormat = date('d/m/Y', strtotime($fecha));
        $mensajeWs = "🔔 *Nuevo turno registrado en Brisa Nails*\n\n"
            . "👤 Nombre: {$nombre}\n"
            . "📞 Teléfono: {$telefono}\n"
            . "💅 Servicio: {$servicio['nombre']}\n"
            . "📅 Fecha: {$fechaFormat}\n"
            . "⏰ Hora: {$hora}\n\n"
            . "_Pendiente de confirmación._";

        NotificacionWhatsApp::enviarADuena($mensajeWs);

        $this->redirigir('?mensaje=Tu turno fue registrado y queda en espera hasta que lo aprobemos.');
    }

    public function apiHorarios(): void
    {
        header('Content-Type: application/json');

        // Sanitizar y validar parámetros de la API
        $fecha      = Seguridad::limpiarString($_GET['fecha']       ?? '', 10);
        $servicioId = Seguridad::enteroPositivo($_GET['servicio_id'] ?? 0);

        if (!Seguridad::fechaValida($fecha) || $servicioId === null) {
            http_response_code(400);
            echo json_encode(['error' => 'Parámetros inválidos']);
            return;
        }
        
        $servicioModelo = new Servicio();
        $servicio = $servicioModelo->buscar($servicioId);
        
        if (!$servicio) {
            echo json_encode(['error' => 'Servicio no encontrado']);
            return;
        }

        // Obtain the day of week for the selected date (0=Sun, 6=Sat)
        $diaSemana = (int) date('w', strtotime($fecha));

        $horarioModelo = new HorarioDisponible();
        $horarioDia = $horarioModelo->buscarPorDia($diaSemana);

        // If no schedule defined or day is inactive, return closed
        if (!$horarioDia || !$horarioDia['activo']) {
            echo json_encode(['disponibles' => [], 'cerrado' => true]);
            return;
        }

        // Duración basada en el servicio seleccionado
        $duracionTurno = (int) $servicio['duracion_minutos'];
        if ($duracionTurno <= 0) $duracionTurno = 60; // fallback seguro

        $turnoModelo = new Turno();
        $ocupados = $turnoModelo->obtenerOcupadosPorFecha($fecha);

        $disponibles = [];
        
        try {
            $horaActual = new DateTime("$fecha " . $horarioDia['hora_inicio']);
            $finDia = new DateTime("$fecha " . $horarioDia['hora_fin']);
            
            while ($horaActual < $finDia) {
                $horaPosibleFin = clone $horaActual;
                $horaPosibleFin->modify("+$duracionTurno minutes");
                
                // El turno no puede terminar después del cierre
                if ($horaPosibleFin > $finDia) {
                    break;
                }
                
                $esValido = true;
                foreach ($ocupados as $oc) {
                    $ocInicio = new DateTime($oc['fecha_hora']);
                    
                    if (!empty($oc['fecha_hora_fin'])) {
                        $ocFin = new DateTime($oc['fecha_hora_fin']);
                    } else {
                        $ocFin = clone $ocInicio;
                        $dur = (int)$oc['duracion_minutos'];
                        $ocFin->modify("+$dur minutes");
                    }
                    
                    if ($horaActual < $ocFin && $horaPosibleFin > $ocInicio) {
                        $esValido = false;
                        break;
                    }
                }
                
                if ($esValido) {
                    $disponibles[] = $horaActual->format('H:i');
                }
                
                // Avanzar bloque a bloque de 90 minutos
                $horaActual->modify("+$duracionTurno minutes");
            }
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Error de fechas']);
            return;
        }
        
        echo json_encode(['disponibles' => $disponibles]);
    }
}
