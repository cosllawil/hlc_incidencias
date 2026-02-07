<?php
require_once dirname(__DIR__) . '/core/Database.php';

class Opinion
{
    public static function crear(array $data): bool
    {
        $db = Database::connect();

        $sql = "INSERT INTO opinion (usuario, descripcion, fecha, hora)
                VALUES (:usuario, :descripcion, :fecha, :hora)";

        return $db->prepare($sql)->execute([
            ':usuario'     => $data['usuario'],
            ':descripcion' => $data['descripcion'],
            ':fecha'       => $data['fecha'],
            ':hora'        => $data['hora'],
        ]);
    }

    public static function listarConUsuarios(): array
    {
        $db = Database::connect();

        $sql = "
      SELECT
        o.id AS Id,
        o.usuario AS Dni,
        COALESCE(u.Nombres, o.usuario) AS Nombres,
        COALESCE(u.Apellidos, '') AS Apellidos,
        o.descripcion AS Descripcion,
        DATE_FORMAT(o.fecha, '%d/%m/%Y') AS Fecha,
        TIME_FORMAT(o.hora, '%H:%i') AS Hora
      FROM opinion o
      LEFT JOIN usuarios u
        ON u.Dni COLLATE utf8mb4_0900_ai_ci = o.usuario COLLATE utf8mb4_0900_ai_ci
      ORDER BY o.id DESC
    ";

        $st = $db->prepare($sql);
        $st->execute();
        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public static function meta(): array
    {
        $db = Database::connect();
        $sql = "SELECT MAX(id) AS lastId, COUNT(*) AS total FROM opinion";
        $st = $db->prepare($sql);
        $st->execute();
        $row = $st->fetch(\PDO::FETCH_ASSOC) ?: ['lastId' => 0, 'total' => 0];

        return [
            'lastId' => (int)($row['lastId'] ?? 0),
            'total'  => (int)($row['total'] ?? 0),
        ];
    }
}
