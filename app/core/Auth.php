<?php

class Auth
{
  public static function user(): ?array
  {
    return $_SESSION['user'] ?? null;
  }


  public static function check(): bool
  {
    return isset($_SESSION['user']) && is_array($_SESSION['user']);
  }


  public static function login(array $user): void
  {
    $_SESSION['user'] = [
      'id'        => (int)($user['id'] ?? 0),
      'Dni'       => (string)($user['Dni'] ?? ''),
      'Nombres'   => (string)($user['Nombres'] ?? ''),
      'Apellidos' => (string)($user['Apellidos'] ?? ''),
      'Rol'       => strtoupper(trim((string)($user['Rol'] ?? 'USUARIO'))),
    ];
  }


  public static function logout(): void
  {
    unset($_SESSION['user']);
  }


  public static function requireLogin(): void
  {
    if (!self::check()) {
      $base = rtrim(BASE_URL, '/');
      header('Location: ' . $base . '/auth/login');
      exit;
    }
  }


  public static function requireRole(array $roles): void
  {
    self::requireLogin();
    $rol = strtoupper(trim((string)($_SESSION['user']['Rol'] ?? 'USUARIO')));
    $allowed = array_map(fn($r) => strtoupper(trim((string)$r)), $roles);

    if (!in_array($rol, $allowed, true)) {
      http_response_code(403);
      echo '403 - No autorizado';
      exit;
    }
  }
}
