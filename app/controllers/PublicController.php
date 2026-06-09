<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Servicio.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Turno.php';

class PublicController extends Controller
{
    public function inicio(): void
    {
        $servicioModelo = new Servicio();

        $this->vista('public/inicio', [
            'servicios' => $servicioModelo->todos(),
            'mensaje' => $_GET['mensaje'] ?? null,
            'error' => $_GET['error'] ?? null,
        ]);
    }

    public function reservar_form(): void
    {
        $servicioModelo = new Servicio();
        
        $this->vista('public/reservar', [
            'servicios' => $servicioModelo->todos(),
            'error' => $_GET['error'] ?? null,
        ]);
    }

    public function reservar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $servicioId = (int) ($_POST['servicio_id'] ?? 0);
        $fecha = trim($_POST['fecha'] ?? '');
        $hora = trim($_POST['hora'] ?? '');

        if ($nombre === '' || $telefono === '' || $servicioId <= 0 || $fecha === '' || $hora === '') {
            $this->redirigir('?accion=reservar_form&error=Completá los campos obligatorios y seleccioná un horario para reservar el turno.');
        }

        $servicioModelo = new Servicio();
        $servicio = $servicioModelo->buscar($servicioId);

        if ($servicio === null) {
            $this->redirigir('?accion=reservar_form&error=El servicio seleccionado no existe.');
        }

        $fechaHora = $fecha . ' ' . $hora . ':00';
        $clienteModelo = new Cliente();
        $turnoModelo = new Turno();

        $clienteId = $clienteModelo->crear($nombre, $telefono, $email);
        $turnoModelo->crear($clienteId, $servicioId, $fechaHora);

        $this->redirigir('?mensaje=Tu turno fue registrado y quedó en espera hasta que lo aprobemos.');
    }

    public function apiHorarios(): void
    {
        header('Content-Type: application/json');
        
        $fecha = $_GET['fecha'] ?? '';
        $servicioId = (int)($_GET['servicio_id'] ?? 0);
        
        if (empty($fecha) || $servicioId <= 0) {
            echo json_encode(['error' => 'Parámetros inválidos']);
            return;
        }
        
        $servicioModelo = new Servicio();
        $servicio = $servicioModelo->buscar($servicioId);
        
        if (!$servicio) {
            echo json_encode(['error' => 'Servicio no encontrado']);
            return;
        }
        
        $duracion = (int)$servicio['duracion_minutos'];
        $turnoModelo = new Turno();
        $ocupados = $turnoModelo->obtenerOcupadosPorFecha($fecha);
        
        // Horarios comerciales (9:00 a 19:00)
        $horaInicio = 9;
        $horaFin = 19;
        $disponibles = [];
        
        try {
            $horaActual = new DateTime("$fecha $horaInicio:00:00");
            $finDia = new DateTime("$fecha $horaFin:00:00");
            
            while ($horaActual < $finDia) {
                $horaPosibleFin = clone $horaActual;
                $horaPosibleFin->modify("+$duracion minutes");
                
                if ($horaPosibleFin > $finDia) {
                    break;
                }
                
                $esValido = true;
                foreach ($ocupados as $oc) {
                    $ocInicio = new DateTime($oc['fecha_hora']);
                    $ocFin = clone $ocInicio;
                    $ocFin->modify("+{$oc['duracion_minutos']} minutes");
                    
                    if ($horaActual < $ocFin && $horaPosibleFin > $ocInicio) {
                        $esValido = false;
                        break;
                    }
                }
                
                if ($esValido) {
                    $disponibles[] = $horaActual->format('H:i');
                }
                
                // Intervalos de la grilla de turnos
                $horaActual->modify('+30 minutes');
            }
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Error de fechas']);
            return;
        }
        
        echo json_encode(['disponibles' => $disponibles]);
    }
}
