<?php

require_once __DIR__ . '/../config/database.php';

class HorarioDisponible
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /**
     * Obtiene todos los horarios configurados, ordenados por día de la semana
     */
    public function todos(): array
    {
        $consulta = $this->db->query('SELECT * FROM horarios_disponibles ORDER BY dia_semana ASC');
        return $consulta->fetchAll();
    }

    /**
     * Obtiene el horario de un día específico (0=Dom, 1=Lun, ..., 6=Sáb)
     */
    public function buscarPorDia(int $diaSemana): ?array
    {
        $consulta = $this->db->prepare('SELECT * FROM horarios_disponibles WHERE dia_semana = :dia');
        $consulta->execute(['dia' => $diaSemana]);
        $resultado = $consulta->fetch();
        return $resultado ?: null;
    }

    /**
     * Guarda o actualiza los horarios de todos los días de la semana
     */
    public function guardarTodos(array $horarios): bool
    {
        $sql = 'INSERT INTO horarios_disponibles (dia_semana, hora_inicio, hora_fin, activo) 
                VALUES (:dia, :inicio, :fin, :activo) 
                ON DUPLICATE KEY UPDATE hora_inicio = :inicio2, hora_fin = :fin2, activo = :activo2';

        $consulta = $this->db->prepare($sql);

        foreach ($horarios as $h) {
            $consulta->execute([
                'dia'     => $h['dia_semana'],
                'inicio'  => $h['hora_inicio'],
                'fin'     => $h['hora_fin'],
                'activo'  => $h['activo'],
                'inicio2' => $h['hora_inicio'],
                'fin2'    => $h['hora_fin'],
                'activo2' => $h['activo'],
            ]);
        }

        return true;
    }
}
