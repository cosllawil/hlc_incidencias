<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Incidente.php';
require_once __DIR__ . '/../models/Catalogo.php';

class IncidentesController extends Controller
{

  public function form(): void
  {
    Auth::requireLogin();
    $categorias = Catalogo::categorias();
    $this->view('incidentes/nuevo', [
      'user' => Auth::user(),
      'categorias' => $categorias,
    ]);
  }


  public function crear(): void
  {
    Auth::requireLogin();
    if (!$this->isPost()) $this->redirect('/incidentes/nuevo');
    $user = Auth::user();
    $categoria = (int)$this->input('categoria_id', 0);
    $titulo = trim((string)$this->input('titulo', ''));
    $detalle = trim((string)$this->input('detalle', ''));
    $prioridad = strtoupper(trim((string)$this->input('prioridad', 'MEDIA')));

    if ($categoria <= 0 || $titulo == '' || $detalle == '') {
      $_SESSION['flash_error'] = 'Completa categoria, titulo y detalle.';
      $this->redirect('/incidentes/nuevo');
    }
    $id = Incidente::create([
      'usuario_id' => (int)$user['id'],
      'categoria_id' => $categoria,
      'titulo' => $titulo,
      'detalle' => $detalle,
      'estado' => 'PENDIENTE',
      'prioridad' => in_array($prioridad, ['BAJA', 'MEDIA', 'ALTA'], true) ? $prioridad : 'MEDIA',
    ]);
    $_SESSION['flash_ok'] = 'Incidente registrado. Codigo #' . $id;
    $this->redirect('/incidentes/mis');
  }


  public function mis(): void
  {
    Auth::requireLogin();
    $user = Auth::user();
    $items = Incidente::byUsuario((int)$user['id']);
    $this->view('incidentes/mis', ['user' => $user, 'items' => $items]);
  }
}
