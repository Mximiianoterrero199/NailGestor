<?php

require_once __DIR__ . '/../config/database.php';

class Cliente
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    public function crear(string $nombre, string $telefono, ?string $email): int
    {
        $consulta = $this->db->prepare(
            'INSERT INTO clientes (nombre, telefono, email) VALUES (:nombre, :telefono, :email)'
        );

        $consulta->execute([
            'nombre' => $nombre,
            'telefono' => $telefono,
            'email' => $email ?: null,
        ]);

        return (int) $this->db->lastInsertId();
    }
}
