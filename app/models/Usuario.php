<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/core/Database.php';

class Usuario
{

    public static function existeDocumento(string $tipo, string $dni): bool
    {
        $db = Database::connect();
        $st = $db->prepare("SELECT 1 FROM usuarios WHERE TipoDocumento = ? AND Dni = ? LIMIT 1");
        $st->execute([$tipo, $dni]);
        return (bool)$st->fetchColumn();
    }

    public static function crear(array $data): bool
    {
        $db = Database::connect();

        $sql = "INSERT INTO usuarios
            (TipoDocumento, Dni, Nombres, Apellidos, Telefono, PasswordHash, Rol,
             IntentosFallidos, BloqueadoHasta, Estado, CreatedAt, UpdatedAt)
            VALUES
            (:TipoDocumento, :Dni, :Nombres, :Apellidos, :Telefono, :PasswordHash, :Rol,
             0, NULL, 'ACTIVO', NOW(), NOW())";

        return $db->prepare($sql)->execute($data);
    }

    public static function findByDni(string $dni): ?array
    {
        $db = Database::connect();
        $st = $db->prepare("SELECT * FROM usuarios WHERE Dni = ? LIMIT 1");
        $st->execute([$dni]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function buscarPorDocumento(string $dni): ?array
    {
        return self::findByDni($dni);
    }

    public static function estaBloqueado(array $user): bool
    {
        if (empty($user['BloqueadoHasta'])) return false;
        return strtotime((string)$user['BloqueadoHasta']) > time();
    }

    public static function setBloqueo(int $id, int $intentos, ?string $bloqueadoHasta): void
    {
        $db = Database::connect();
        $st = $db->prepare(
            "UPDATE usuarios
             SET IntentosFallidos = ?, BloqueadoHasta = ?, UpdatedAt = NOW()
             WHERE id = ?"
        );
        $st->execute([$intentos, $bloqueadoHasta, $id]);
    }

    public static function resetIntentos(int $id): void
    {
        $db = Database::connect();
        $st = $db->prepare(
            "UPDATE usuarios
             SET IntentosFallidos = 0, BloqueadoHasta = NULL, UpdatedAt = NOW()
             WHERE id = ?"
        );
        $st->execute([$id]);
    }

    public static function updateLoginMeta(int $id, string $ip): void
    {
        $db = Database::connect();
        $st = $db->prepare(
            "UPDATE usuarios
             SET UltimoLogin = NOW(), UltimoIP = ?, UpdatedAt = NOW()
             WHERE id = ?"
        );
        $st->execute([$ip, $id]);
    }

    public static function registrarLoginExitoso(int $id, string $ip): void
    {
        self::resetIntentos($id);
        self::updateLoginMeta($id, $ip);
    }

    public static function registrarFallo(int $id, int $maxIntentos = 5, int $minutosBloqueo = 15): array
    {
        $db = Database::connect();
        $st = $db->prepare("SELECT IntentosFallidos FROM usuarios WHERE id=? LIMIT 1");
        $st->execute([$id]);
        $actual = (int)($st->fetchColumn() ?? 0);

        $intentos = $actual + 1;
        $bloqueado = false;
        $bloqueadoHasta = null;

        if ($intentos >= $maxIntentos) {
            $bloqueado = true;
            $bloqueadoHasta = date('Y-m-d H:i:s', time() + ($minutosBloqueo * 60));
        }

        self::setBloqueo($id, $intentos, $bloqueadoHasta);

        return [
            'intentos' => $intentos,
            'bloqueado' => $bloqueado,
            'bloqueadoHasta' => $bloqueadoHasta
        ];
    }

    public static function getPasswordHashById(int $id): ?string
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT PasswordHash FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['PasswordHash'] ?? null;
    }

    public static function updatePasswordHash(int $id, string $hash): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare("UPDATE usuarios SET PasswordHash = ? WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }

    public static function actualizarPassword(int $id, string $passwordHash): bool
    {
        $db = Database::connect();
        $st = $db->prepare("UPDATE usuarios SET PasswordHash = ?, UpdatedAt = NOW() WHERE id = ? LIMIT 1");

        return (bool)$st->execute([$passwordHash, $id]);
    }

    public static function updateRolByDni(string $dni, string $rol): bool
    {
        $db = Database::connect();
        $st = $db->prepare("UPDATE usuarios SET Rol = ?, UpdatedAt = NOW() WHERE Dni = ? LIMIT 1");
        return $st->execute([$rol, $dni]);
    }

    public static function findById(int $id): ?array
    {
        $db = Database::connect();

        $sql = "
        SELECT 
            Dni,
            Nombres,
            Apellidos,
            Telefono
        FROM usuarios
        WHERE id = :id
        LIMIT 1
    ";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function updateTelefonoById(int $id, string $telefono): bool
    {
        $db = Database::connect();

        $sql = "
        UPDATE usuarios
        SET Telefono = :telefono
        WHERE id = :id
    ";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
