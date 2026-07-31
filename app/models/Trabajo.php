<?php

require_once __DIR__ . '/../config/database.php';

class Trabajo
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    public function todos(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM trabajos ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function crear(string $imagen, ?string $titulo = null): bool
    {
        $stmt = $this->db->prepare("INSERT INTO trabajos (imagen, titulo) VALUES (?, ?)");
        return $stmt->execute([$imagen, $titulo]);
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM trabajos WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function buscar(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM trabajos WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch() ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function actualizarTitulo(int $id, ?string $titulo = null): bool
    {
        $stmt = $this->db->prepare("UPDATE trabajos SET titulo = ? WHERE id = ?");
        return $stmt->execute([$titulo, $id]);
    }
}
