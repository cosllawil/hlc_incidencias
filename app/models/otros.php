<?php
require_once __DIR__ . '/../core/Database.php';

class Otros
{
    public static function listar(): array
    {
        $db = Database::connect();
        $sql = "SELECT id, nombre FROM otros ORDER BY nombre ASC";
        $stmt = $db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
}
