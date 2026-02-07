<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Incidente.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Opinion.php';

class AdminController extends Controller
{
  public function dashboard(): void
  {
    $this->bandeja();
  }


  public function bandeja(): void
  {
    Auth::requireRole(['ADMIN', 'SOPORTE']);
    $items = Incidente::all();
    $this->view('admin/dashboard', ['user' => Auth::user(), 'items' => $items]);
  }


  public function cambiarEstado(): void
  {
    Auth::requireRole(['ADMIN']);
    if (!$this->isPost()) {
      http_response_code(405);
      exit;
    }

    $id = (int)$this->input('id', 0);
    $estado = strtoupper(trim((string)$this->input('estado', '')));
    if ($id <= 0 || $estado !== 'ATENDIDO') {
      echo json_encode(['ok' => false, 'message' => 'Datos inválidos']);
      exit;
    }
    $user = Auth::user();
    $adminId = (int)($user['id'] ?? 0);
    $adminNombre = trim(($user['Nombres'] ?? '') . ' ' . ($user['Apellidos'] ?? ''));
    $ok = Incidente::updateEstado($id, 'ATENDIDO', $adminId, $adminNombre);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
      'ok' => (bool)$ok,
      'atendido_por' => $adminNombre
    ], JSON_UNESCAPED_UNICODE);
    exit;
  }


  public function usuarios(): void
  {
    Auth::requireRole(['ADMIN']);
    $isAjax =
      (($_GET['ajax'] ?? '') === '1') ||
      (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest') ||
      (strpos(strtolower($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json') !== false);
    if ($isAjax) {
      header('Content-Type: application/json; charset=utf-8');
      if (!$this->isPost() && isset($_GET['dni'])) {
        $dni = preg_replace('/\D+/', '', (string)($_GET['dni'] ?? ''));
        if ($dni === '' || strlen($dni) !== 8) {
          echo json_encode(['ok' => false, 'message' => 'DNI inválido. Debe tener 8 dígitos.']);
          return;
        }
        $u = Usuario::findByDni($dni);
        if (!$u) {
          echo json_encode(['ok' => false, 'message' => 'No se encontró el DNI en la tabla usuarios.']);
          return;
        }
        echo json_encode([
          'ok' => true,
          'data' => [
            'Dni'       => (string)$u['Dni'],
            'Nombres'   => (string)$u['Nombres'],
            'Apellidos' => (string)$u['Apellidos'],
            'Rol'       => strtoupper((string)$u['Rol']),
          ]
        ]);
        return;
      }
      if ($this->isPost()) {
        $dni = preg_replace('/\D+/', '', (string)$this->input('Dni', ''));
        $rol = strtoupper(trim((string)$this->input('Rol', '')));
        $allowedRoles = ['ADMIN', 'PERSONAL'];
        if ($dni === '' || strlen($dni) !== 8) {
          echo json_encode(['ok' => false, 'message' => 'DNI inválido. Debe tener 8 dígitos.']);
          return;
        }
        if (!in_array($rol, $allowedRoles, true)) {
          echo json_encode(['ok' => false, 'message' => 'Rol inválido.']);
          return;
        }
        $u = Usuario::findByDni($dni);
        if (!$u) {
          echo json_encode(['ok' => false, 'message' => 'El usuario no existe.']);
          return;
        }
        $ok = Usuario::updateRolByDni($dni, $rol);
        if (!$ok) {
          echo json_encode(['ok' => false, 'message' => 'No se pudo actualizar el rol.']);
          return;
        }
        echo json_encode(['ok' => true, 'message' => 'Rol actualizado correctamente.']);
        return;
      }
      echo json_encode(['ok' => false, 'message' => 'Solicitud AJAX no válida.']);
      return;
    }
    $this->view('admin/usuarios', [
      'user' => Auth::user()
    ]);
  }


  public function veropinion(): void
  {
    Auth::requireRole(['ADMIN']);
    $isAjax = (($_GET['ajax'] ?? '') === '1');
    if ($isAjax) {
      header('Content-Type: application/json; charset=utf-8');

      if (($_GET['meta'] ?? '') === '1') {
        $m = Opinion::meta();
        echo json_encode(['ok' => true] + $m, JSON_UNESCAPED_UNICODE);
        return;
      }
      $items = Opinion::listarConUsuarios();
      $m = Opinion::meta();
      echo json_encode([
        'ok'     => true,
        'lastId' => $m['lastId'],
        'total'  => $m['total'],
        'items'  => $items
      ], JSON_UNESCAPED_UNICODE);
      return;
    }
    $items = Opinion::listarConUsuarios();
    $m = Opinion::meta();
    $this->view('admin/veropinion', [
      'user'   => Auth::user(),
      'items'  => $items,
      'total'  => $m['total'],
      'lastId' => $m['lastId'],
    ]);
  }


  public function atender(): void
  {
    Auth::requireRole(['ADMIN']);
    $isAjax = (($_GET['ajax'] ?? '') === '1');
    if ($isAjax) {
      header('Content-Type: application/json; charset=utf-8');
      if (($_GET['meta'] ?? '') === '1') {
        $m = Incidente::semanaActualMeta();
        echo json_encode(['ok' => true] + $m, JSON_UNESCAPED_UNICODE);
        return;
      }
      $items = Incidente::semanaActualListado();
      $m = Incidente::semanaActualMeta();
      echo json_encode([
        'ok'     => true,
        'lastId' => $m['lastId'],
        'total'  => $m['total'],
        'items'  => $items
      ], JSON_UNESCAPED_UNICODE);
      return;
    }
    $items = Incidente::semanaActualListado();
    $m = Incidente::semanaActualMeta();

    $this->view('admin/atender', [
      'user'   => Auth::user(),
      'items'  => $items,
      'total'  => $m['total'],
      'lastId' => $m['lastId'],
    ]);
  }


  public function verdetalles(): void
  {
    Auth::requireRole(['ADMIN']);
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
      header('Location: ' . BASE_URL . '/admin/atender');
      return;
    }
    $inc = Incidente::detallePorId($id);
    if (!$inc) {
      header('Location: ' . BASE_URL . '/admin/atender');
      return;
    }
    $this->view('admin/verdetalles', [
      'user' => Auth::user(),
      'inc'  => $inc,
    ]);
  }
}
