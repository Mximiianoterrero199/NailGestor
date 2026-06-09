<?php

require_once __DIR__ . '/../config/database.php';

class Servicio
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    public function todos(): array
    {
        $consulta = $this->db->query('SELECT * FROM servicios ORDER BY nombre ASC');
        return $consulta->fetchAll();
    }

    public function buscar(int $id): ?array
    {
        $consulta = $this->db->prepare('SELECT * FROM servicios WHERE id = :id');
        $consulta->execute(['id' => $id]);
        $servicio = $consulta->fetch();

        return $servicio ?: null;
    }
}
