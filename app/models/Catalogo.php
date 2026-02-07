<?php

require_once __DIR__ . '/../core/Database.php';

class Catalogo
{

  public static function categorias(): array
  {
    $pdo = Database::pdo();
    return $pdo
      ->query("SELECT id, nombre FROM categorias WHERE activo=1 ORDER BY nombre")
      ->fetchAll();
  }

  public static function servicios(): array
  {
    $pdo = Database::pdo();
    return $pdo
      ->query("SELECT id, nombre FROM servicios ORDER BY nombre")
      ->fetchAll();
  }
}
