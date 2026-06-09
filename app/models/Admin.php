<?php

require_once __DIR__ . '/../config/database.php';

class Admin
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    public function buscarPorUsuario(string $usuario): ?array
    {
        $consulta = $this->db->prepare(
            'SELECT * FROM admins WHERE usuario = :usuario LIMIT 1'
        );
        $consulta->execute(['usuario' => $usuario]);
        $admin = $consulta->fetch();

        return $admin ?: null;
    }

    public function existeUsuario(string $usuario): bool
    {
        $consulta = $this->db->prepare(
            'SELECT COUNT(*) FROM admins WHERE usuario = :usuario'
        );
        $consulta->execute(['usuario' => $usuario]);

        return (int) $consulta->fetchColumn() > 0;
    }

    public function crear(string $usuario, string $claveTextoPlano): bool
    {
        $hash = password_hash($claveTextoPlano, PASSWORD_BCRYPT);

        $consulta = $this->db->prepare(
            'INSERT INTO admins (usuario, clave) VALUES (:usuario, :clave)'
        );

        return $consulta->execute([
            'usuario' => $usuario,
            'clave'   => $hash,
        ]);
    }

    public function verificarClave(string $claveTextoPlano, string $hash): bool
    {
        return password_verify($claveTextoPlano, $hash);
    }
}
