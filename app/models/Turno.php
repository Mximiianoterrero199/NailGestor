<?php

require_once __DIR__ . '/../config/database.php';

class Turno
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    public function crear(int $clienteId, int $servicioId, string $fechaHora): bool
    {
        $consulta = $this->db->prepare(
            'INSERT INTO turnos (cliente_id, servicio_id, fecha_hora) VALUES (:cliente_id, :servicio_id, :fecha_hora)'
        );

        return $consulta->execute([
            'cliente_id' => $clienteId,
            'servicio_id' => $servicioId,
            'fecha_hora' => $fechaHora,
        ]);
    }

    public function todosOrdenadosPorFecha(): array
    {
        $sql = "SELECT turnos.*, clientes.nombre AS cliente_nombre, clientes.telefono, clientes.email,
                       servicios.nombre AS servicio_nombre, servicios.precio, servicios.duracion_minutos
                FROM turnos
                INNER JOIN clientes ON clientes.id = turnos.cliente_id
                INNER JOIN servicios ON servicios.id = turnos.servicio_id
                ORDER BY turnos.fecha_hora ASC";

        $consulta = $this->db->query($sql);
        return $consulta->fetchAll();
    }

    public function cambiarEstado(int $id, string $estado): bool
    {
        $estadosPermitidos = ['pendiente', 'confirmado', 'cancelado', 'completado'];

        if (!in_array($estado, $estadosPermitidos, true)) {
            return false;
        }

        $consulta = $this->db->prepare('UPDATE turnos SET estado = :estado WHERE id = :id');

        return $consulta->execute([
            'estado' => $estado,
            'id' => $id,
        ]);
    }

    public function obtenerOcupadosPorFecha(string $fecha): array
    {
        $consulta = $this->db->prepare("
            SELECT turnos.fecha_hora, servicios.duracion_minutos
            FROM turnos
            INNER JOIN servicios ON servicios.id = turnos.servicio_id
            WHERE DATE(turnos.fecha_hora) = :fecha
            AND turnos.estado != 'cancelado'
        ");
        $consulta->execute(['fecha' => $fecha]);
        return $consulta->fetchAll();
    }

    public function buscarConDetalles(int $id): ?array
    {
        $sql = "SELECT turnos.*, clientes.nombre AS cliente_nombre, clientes.email,
                       servicios.nombre AS servicio_nombre
                FROM turnos
                INNER JOIN clientes ON clientes.id = turnos.cliente_id
                INNER JOIN servicios ON servicios.id = turnos.servicio_id
                WHERE turnos.id = :id
                LIMIT 1";

        $consulta = $this->db->prepare($sql);
        $consulta->execute(['id' => $id]);
        
        $resultado = $consulta->fetch();
        return $resultado ?: null;
    }
}
