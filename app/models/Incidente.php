<?php
require_once dirname(__DIR__) . '/core/Database.php';

class Incidente
{
    public static function all(): array
    {
        $db = Database::connect();

        $sql = "
            SELECT 
                i.*,
                u.Nombres   AS usuario_nombres,
                u.Apellidos AS usuario_apellidos,
                a.Nombres   AS atendido_nombres,
                a.Apellidos AS atendido_apellidos
            FROM incidentes i
            LEFT JOIN usuarios u ON u.id = i.usuario_id
            LEFT JOIN usuarios a ON a.id = i.atendido_por
            ORDER BY i.created_at DESC
        ";

        return $db->query($sql)->fetchAll();
    }

    public static function updateEstado(
        int $id,
        string $estado,
        int $adminId,
        string $adminNombre
    ): bool {
        $db = Database::connect();

        $sql = "
            UPDATE incidentes
            SET
                estado = :estado,
                atendido_por = :adminId,
                atendido = :adminNombre,
                atendido_en = NOW(),
                updated_at = NOW()
            WHERE id = :id
            LIMIT 1
        ";

        return $db->prepare($sql)->execute([
            ':estado'      => $estado,
            ':adminId'     => $adminId,
            ':adminNombre' => $adminNombre,
            ':id'          => $id,
        ]);
    }

    public static function byUser(int $usuarioId): array
    {
        $db = Database::connect();
        $st = $db->prepare("SELECT * FROM incidentes WHERE usuario_id = ? ORDER BY created_at DESC");
        $st->execute([$usuarioId]);
        return $st->fetchAll();
    }

    public static function create(array $data): bool
    {
        $db = Database::connect();
        $sql = "INSERT INTO incidentes (usuario_id, titulo, descripcion, prioridad, estado, created_at, updated_at)
                VALUES (:usuario_id, :titulo, :descripcion, :prioridad, 'PENDIENTE', NOW(), NOW())";
        return $db->prepare($sql)->execute($data);
    }

    public static function crearDesdeComputadora(array $data)
    {
        $db = Database::connect();

        $usuario      = $data['usuario']      ?? '';
        $oficina      = $data['oficina']      ?? '';
        $tipoproblema = $data['tipoproblema'] ?? '';
        $descripcion  = $data['descripcion']  ?? '';
        $foto         = $data['foto']         ?? '';
        $telefono     = $data['telefono']     ?? '';
        $fecha        = $data['fecha']        ?? date('Y-m-d');
        $hora         = $data['hora']         ?? date('H:i:s');


        $estado = $data['estado'] ?? 'PENDIENTE';

        $atendido = $data['atendido'] ?? '';
        if ($atendido === null) $atendido = '';

        $atendido_por = $data['atendido_por'] ?? 0;
        if ($atendido_por === null || $atendido_por === '') $atendido_por = 0;

        $atendido_en = $data['atendido_en'] ?? null;

        $usuario_id = $data['usuario_id'] ?? null;

        $sql = "INSERT INTO incidentes
          (usuario, oficina, tipoproblema, descripcion, foto, telefono, fecha, hora, estado, atendido, usuario_id, atendido_por, atendido_en, created_at, updated_at)
          VALUES
          (:usuario, :oficina, :tipoproblema, :descripcion, :foto, :telefono, :fecha, :hora, :estado, :atendido, :usuario_id, :atendido_por, :atendido_en, NOW(), NOW())";

        $stmt = $db->prepare($sql);

        $ok = $stmt->execute([
            ':usuario'      => $usuario,
            ':oficina'      => $oficina,
            ':tipoproblema' => $tipoproblema,
            ':descripcion'  => $descripcion,
            ':foto'         => $foto,
            ':telefono'     => $telefono,
            ':fecha'        => $fecha,
            ':hora'         => $hora,
            ':estado'       => $estado,
            ':atendido'     => $atendido,
            ':usuario_id'   => $usuario_id,
            ':atendido_por' => $atendido_por,
            ':atendido_en'  => $atendido_en,
        ]);

        return $ok ? (int)$db->lastInsertId() : false;
    }

    public static function listarCamposHistorialPorUsuario(int $usuarioId): array
    {
        $db = Database::connect();

        $sql = "
            SELECT oficina, tipoproblema, estado, fecha, hora
            FROM incidentes
            WHERE usuario_id = :usuario_id
            ORDER BY fecha DESC, hora DESC
        ";

        $st = $db->prepare($sql);
        $st->execute([':usuario_id' => $usuarioId]);
        return $st->fetchAll();
    }

    private static function semanaInicioSQL(): string
    {

        return "DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)";
    }

    public static function semanaActualListado(): array
    {
        $db = Database::connect();

        $sql = "
            SELECT
              i.id AS Id,
              i.oficina AS Oficina,
              i.tipoproblema AS TipoProblema,
              i.estado AS Estado,
              i.usuario AS Dni,
              COALESCE(u.Nombres, '') AS Nombres,
              COALESCE(u.Apellidos, '') AS Apellidos,
              COALESCE(u.Telefono, '') AS Telefono
            FROM incidentes i
            LEFT JOIN usuarios u
              ON u.Dni COLLATE utf8mb4_0900_ai_ci = i.usuario COLLATE utf8mb4_0900_ai_ci
            WHERE
              i.created_at >= " . self::semanaInicioSQL() . "
              AND i.created_at <  DATE_ADD(" . self::semanaInicioSQL() . ", INTERVAL 7 DAY)
            ORDER BY i.id DESC
        ";

        $st = $db->prepare($sql);
        $st->execute();
        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public static function detallePorId(int $id): ?array
    {
        $db = Database::connect();

        $sql = "
            SELECT
                id,
                oficina,
                tipoproblema,
                descripcion,
                foto,
                estado,
                created_at
            FROM incidentes
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public static function semanaActualMeta(): array
    {
        $db = Database::connect();

        $sql = "
        SELECT
          COALESCE(MAX(id),0) AS lastId,
          COUNT(*) AS total
        FROM incidentes
        WHERE
          created_at >= " . self::semanaInicioSQL() . "
          AND created_at <  DATE_ADD(" . self::semanaInicioSQL() . ", INTERVAL 7 DAY)
          AND UPPER(TRIM(estado)) = 'PENDIENTE'
    ";

        $st = $db->prepare($sql);
        $st->execute();
        $row = $st->fetch(\PDO::FETCH_ASSOC) ?: ['lastId' => 0, 'total' => 0];

        return [
            'lastId' => (int)($row['lastId'] ?? 0),
            'total'  => (int)($row['total'] ?? 0),
        ];
    }

    public static function byUserSemanaActual(int $usuarioId): array
    {
        $db = Database::connect();

        $sql = "
            SELECT oficina, tipoproblema, estado, fecha, hora, created_at, updated_at
            FROM incidentes
            WHERE usuario_id = :usuario_id
              AND created_at >= " . self::semanaInicioSQL() . "
              AND created_at <  DATE_ADD(" . self::semanaInicioSQL() . ", INTERVAL 7 DAY)
            ORDER BY created_at DESC
        ";

        $st = $db->prepare($sql);
        $st->execute([':usuario_id' => $usuarioId]);

        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public static function pendientes(): array
    {
        $db = Database::connect();

        $sql = "
            SELECT 
                i.*,
                u.Nombres   AS usuario_nombres,
                u.Apellidos AS usuario_apellidos
            FROM incidentes i
            LEFT JOIN usuarios u ON u.id = i.usuario_id
            WHERE UPPER(TRIM(i.estado)) = 'PENDIENTE'
            ORDER BY i.created_at ASC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}
