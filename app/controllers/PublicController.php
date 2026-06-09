<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Servicio.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Turno.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

        if (!empty($email)) {
            $this->enviarConfirmacionReserva($email, $nombre, $servicio['nombre'], $fechaHora);
        }

        $this->redirigir('?mensaje=Tu turno fue registrado y quedó en espera hasta que lo aprobemos.');
    }

    private function enviarConfirmacionReserva(string $destinatario, string $cliente, string $servicio, string $fechaHora): void
    {
        $fechaFormateada = date('d/m/Y H:i', strtotime($fechaHora));
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $this->config['smtp']['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->config['smtp']['usuario'];
            $mail->Password   = $this->config['smtp']['clave'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $this->config['smtp']['puerto'];
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($this->config['smtp']['usuario'], $this->config['app_nombre']);
            $mail->addAddress($destinatario, $cliente);

            $mail->isHTML(true);
            $mail->Subject = "Solicitud de turno recibida - " . $this->config['app_nombre'];

            $mensajeHtml = "<h2>Hola $cliente,</h2>";
            $mensajeHtml .= "<p>Hemos recibido tu solicitud de turno para <strong>'$servicio'</strong> el día <strong>$fechaFormateada</strong>.</p>";
            $mensajeHtml .= "<p>Tu turno se encuentra <strong>PENDIENTE</strong> de confirmación por nuestro equipo. Te avisaremos cuando sea confirmado o si hay algún cambio.</p>";
            $mensajeHtml .= "<br><p>Saludos cordiales,<br>El equipo de <strong>" . $this->config['app_nombre'] . "</strong></p>";

            $mail->Body    = $mensajeHtml;
            $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $mensajeHtml));

            $mail->send();
        } catch (Exception $e) {
            // Error silently ignored to not interrupt booking process
        }
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
                
                $horaActual->modify('+30 minutes');
            }
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Error de fechas']);
            return;
        }
        
        echo json_encode(['disponibles' => $disponibles]);
    }
}
