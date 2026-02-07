<?php
require_once __DIR__ . '/../core/Database.php';

class Telefono
{
    public static function listar(): array
    {
        $db = Database::connect();
        $sql = "SELECT id, nombre FROM telefono ORDER BY nombre ASC";
        $stmt = $db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
}
