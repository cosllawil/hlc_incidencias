<?php
require_once __DIR__ . '/../core/Controller.php';

class HomeController extends Controller
{
    public function landing(): void
    {
        $this->view('welcome/index');
    }


    public function dashboard(): void
    {
        require_once __DIR__ . '/../core/Auth.php';
        Auth::requireLogin();
        $user = $_SESSION['user'] ?? [];
        $this->view('home/dashboard', ['user' => $user]);
    }



    public function computadora(): void
    {
        require_once __DIR__ . '/../core/Auth.php';
        Auth::requireLogin();
        require_once __DIR__ . '/../models/Catalogo.php';
        require_once __DIR__ . '/../models/Incidente.php';
        $user = Auth::user() ?? [];
        $categorias = Catalogo::categorias();
        $servicios  = Catalogo::servicios();
        $isPost = (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST');
        if ($isPost) {
            date_default_timezone_set('America/Lima');
            $oficina      = trim((string)$this->input('Oficina', ''));
            $tipoproblema = trim((string)$this->input('TipoProblema', ''));
            $descripcion  = trim((string)$this->input('Descripcion', ''));
            $telefono     = preg_replace('/\D+/', '', (string)$this->input('Telefono', ''));
            $errores = [];
            if ($oficina === '')      $errores[] = 'Seleccione la oficina.';
            if ($tipoproblema === '') $errores[] = 'Seleccione el tipo de problema.';
            if ($descripcion === '' || mb_strlen($descripcion) < 5) $errores[] = 'Ingrese una descripción (mín. 5 caracteres).';
            if ($telefono === '' || strlen($telefono) !== 9) $errores[] = 'Ingrese un teléfono válido de 9 dígitos.';
            $rutaFoto = null;
            if (!isset($_FILES['Foto']) || $_FILES['Foto']['error'] === UPLOAD_ERR_NO_FILE) {
                $errores[] = 'Seleccione una foto.';
            } else {
                if ($_FILES['Foto']['error'] !== UPLOAD_ERR_OK) {
                    $errores[] = 'No se pudo subir la foto.';
                } else {
                    $maxMB = 5;
                    if ((int)$_FILES['Foto']['size'] > $maxMB * 1024 * 1024) {
                        $errores[] = "La foto supera ${maxMB}MB.";
                    }
                    $ext = strtolower(pathinfo((string)$_FILES['Foto']['name'], PATHINFO_EXTENSION));
                    $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

                    if (!in_array($ext, $permitidas, true)) {
                        $errores[] = 'Formato de foto no permitido (jpg, png, webp).';
                    }
                    if (empty($errores)) {
                        $dir = dirname(__DIR__, 2) . '/public/uploads/incidentes';
                        if (!is_dir($dir)) {
                            mkdir($dir, 0777, true);
                        }
                        $nombre  = 'inc_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                        $destino = $dir . '/' . $nombre;

                        if (!move_uploaded_file($_FILES['Foto']['tmp_name'], $destino)) {
                            $errores[] = 'No se pudo guardar la foto.';
                        } else {
                            $rutaFoto = '/uploads/incidentes/' . $nombre;
                        }
                    }
                }
            }
            if (!empty($errores)) {
                $_SESSION['toast_error'] = implode('<br>', array_map('htmlspecialchars', $errores));
                $this->redirect('/home/computadora');
                return;
            }
            $usuarioNombre = trim(($user['Dni'] ?? ''));
            if ($usuarioNombre === '') $usuarioNombre = 'Usuario';

            $usuarioId = (int)($user['id'] ?? 0);
            if ($usuarioId <= 0) {
                $_SESSION['toast_error'] = 'Sesión inválida. Vuelva a iniciar sesión.';
                $this->redirect('/auth/login');
                return;
            }
            $id = Incidente::crearDesdeComputadora([
                'usuario'      => $usuarioNombre,
                'oficina'      => $oficina,
                'tipoproblema' => $tipoproblema,
                'descripcion'  => $descripcion,
                'foto'         => $rutaFoto,
                'telefono'     => $telefono,
                'fecha'        => date('Y-m-d'),
                'hora'         => date('H:i:s'),
                'estado'       => 'Pendiente',
                'atendido'     => null,
                'usuario_id'   => $usuarioId,
                'atendido_por' => null,
                'atendido_en'  => null,
            ]);
            if (!$id) {
                $_SESSION['toast_error'] = 'No se pudo registrar la incidencia. Intente nuevamente.';
                $this->redirect('/home/computadora');
                return;
            }
            $_SESSION['toast_ok'] = 'Incidencia registrada correctamente. Código #' . $id;
            $this->redirect('/home/computadora');
            return;
        }
        $this->view('home/computadora', [
            'user'       => $user,
            'categorias' => $categorias,
            'servicios'  => $servicios
        ]);
    }


    public function impresora(): void
    {
        require_once __DIR__ . '/../core/Auth.php';
        Auth::requireLogin();
        require_once __DIR__ . '/../models/Catalogo.php';
        require_once __DIR__ . '/../models/Impresora.php';
        require_once __DIR__ . '/../models/Incidente.php';
        $user = Auth::user() ?? [];
        $servicios  = Catalogo::servicios();
        $impresoras = Impresora::listar();
        $isPost = (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST');
        if ($isPost) {
            date_default_timezone_set('America/Lima');
            $oficina      = trim((string)$this->input('Oficina', ''));
            $tipoproblema = trim((string)$this->input('TipoProblema', ''));
            $descripcion  = trim((string)$this->input('Descripcion', ''));
            $telefono     = preg_replace('/\D+/', '', (string)$this->input('Telefono', ''));
            $errores = [];
            if ($oficina === '')      $errores[] = 'Seleccione la oficina.';
            if ($tipoproblema === '') $errores[] = 'Seleccione el tipo de problema.';
            if ($descripcion === '' || mb_strlen($descripcion) < 5) $errores[] = 'Ingrese una descripción (mín. 5 caracteres).';
            if ($telefono === '' || strlen($telefono) !== 9) $errores[] = 'Ingrese un teléfono válido de 9 dígitos.';
            $rutaFoto = null;
            if (!isset($_FILES['Foto']) || $_FILES['Foto']['error'] === UPLOAD_ERR_NO_FILE) {
                $errores[] = 'Seleccione una foto.';
            } else {
                if ($_FILES['Foto']['error'] !== UPLOAD_ERR_OK) {
                    $errores[] = 'No se pudo subir la foto.';
                } else {
                    $maxMB = 5;
                    if ((int)$_FILES['Foto']['size'] > $maxMB * 1024 * 1024) {
                        $errores[] = "La foto supera ${maxMB}MB.";
                    }
                    $ext = strtolower(pathinfo((string)$_FILES['Foto']['name'], PATHINFO_EXTENSION));
                    $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

                    if (!in_array($ext, $permitidas, true)) {
                        $errores[] = 'Formato de foto no permitido (jpg, png, webp).';
                    }
                    if (empty($errores)) {
                        $dir = dirname(__DIR__, 2) . '/public/uploads/incidentes';
                        if (!is_dir($dir)) mkdir($dir, 0777, true);

                        $nombre  = 'inc_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                        $destino = $dir . '/' . $nombre;

                        if (!move_uploaded_file($_FILES['Foto']['tmp_name'], $destino)) {
                            $errores[] = 'No se pudo guardar la foto.';
                        } else {
                            $rutaFoto = '/uploads/incidentes/' . $nombre;
                        }
                    }
                }
            }
            if (!empty($errores)) {
                $_SESSION['toast_error'] = implode('<br>', array_map('htmlspecialchars', $errores));
                $this->redirect('/home/impresora');
                return;
            }
            $usuarioNombre = trim(($user['Dni'] ?? 'Usuario'));
            $usuarioId     = (int)($user['id'] ?? 0);
            $id = Incidente::crearDesdeComputadora([
                'usuario'      => $usuarioNombre,
                'oficina'      => $oficina,
                'tipoproblema' => $tipoproblema,
                'descripcion'  => $descripcion,
                'foto'         => $rutaFoto,
                'telefono'     => $telefono,
                'fecha'        => date('Y-m-d'),
                'hora'         => date('H:i:s'),
                'estado'       => 'Pendiente',
                'atendido'     => null,
                'usuario_id'   => $usuarioId,
                'atendido_por' => null,
                'atendido_en'  => null,
            ]);
            if (!$id) {
                $_SESSION['toast_error'] = 'No se pudo registrar la incidencia.';
                $this->redirect('/home/impresora');
                return;
            }
            $_SESSION['toast_ok'] = 'Incidencia registrada correctamente. Código #' . $id;
            $this->redirect('/home/impresora');
            return;
        }
        $this->view('home/impresora', [
            'user'       => $user,
            'servicios'  => $servicios,
            'impresoras' => $impresoras
        ]);
    }


    public function internet(): void
    {
        require_once __DIR__ . '/../core/Auth.php';
        Auth::requireLogin();
        require_once __DIR__ . '/../models/Catalogo.php';
        require_once __DIR__ . '/../models/Internet.php';
        require_once __DIR__ . '/../models/Incidente.php';
        $user = Auth::user() ?? [];
        $servicios = Catalogo::servicios();
        $internet  = Internet::listar();
        $isPost = (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST');
        if ($isPost) {
            date_default_timezone_set('America/Lima');
            $oficina      = trim((string)$this->input('Oficina', ''));
            $tipoproblema = trim((string)$this->input('TipoProblema', ''));
            $descripcion  = trim((string)$this->input('Descripcion', ''));
            $telefono     = preg_replace('/\D+/', '', (string)$this->input('Telefono', ''));
            $errores = [];
            if ($oficina === '')      $errores[] = 'Seleccione la oficina.';
            if ($tipoproblema === '') $errores[] = 'Seleccione el tipo de problema.';
            if ($descripcion === '' || mb_strlen($descripcion) < 5) $errores[] = 'Ingrese una descripción (mín. 5 caracteres).';
            if ($telefono === '' || strlen($telefono) !== 9) $errores[] = 'Ingrese un teléfono válido de 9 dígitos.';
            $rutaFoto = null;
            if (!isset($_FILES['Foto']) || $_FILES['Foto']['error'] === UPLOAD_ERR_NO_FILE) {
                $errores[] = 'Seleccione una foto.';
            } else {
                if ($_FILES['Foto']['error'] !== UPLOAD_ERR_OK) {
                    $errores[] = 'No se pudo subir la foto.';
                } else {
                    $maxMB = 5;
                    if ((int)$_FILES['Foto']['size'] > $maxMB * 1024 * 1024) {
                        $errores[] = "La foto supera ${maxMB}MB.";
                    }
                    $ext = strtolower(pathinfo((string)$_FILES['Foto']['name'], PATHINFO_EXTENSION));
                    $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

                    if (!in_array($ext, $permitidas, true)) {
                        $errores[] = 'Formato de foto no permitido (jpg, png, webp).';
                    }
                    if (empty($errores)) {
                        $dir = dirname(__DIR__, 2) . '/public/uploads/incidentes';
                        if (!is_dir($dir)) mkdir($dir, 0777, true);

                        $nombre  = 'inc_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                        $destino = $dir . '/' . $nombre;

                        if (!move_uploaded_file($_FILES['Foto']['tmp_name'], $destino)) {
                            $errores[] = 'No se pudo guardar la foto.';
                        } else {
                            $rutaFoto = '/uploads/incidentes/' . $nombre;
                        }
                    }
                }
            }
            if (!empty($errores)) {
                $_SESSION['toast_error'] = implode('<br>', array_map('htmlspecialchars', $errores));
                $this->redirect('/home/internet');
                return;
            }
            $usuarioNombre = trim(($user['Dni'] ?? 'Usuario'));
            if ($usuarioNombre === '') $usuarioNombre = 'Usuario';
            $usuarioId = (int)($user['id'] ?? 0);
            $id = Incidente::crearDesdeComputadora([
                'usuario'      => $usuarioNombre,
                'oficina'      => $oficina,
                'tipoproblema' => $tipoproblema,
                'descripcion'  => $descripcion,
                'foto'         => $rutaFoto,
                'telefono'     => $telefono,
                'fecha'        => date('Y-m-d'),
                'hora'         => date('H:i:s'),
                'estado'       => 'Pendiente',
                'atendido'     => null,
                'usuario_id'   => $usuarioId,
                'atendido_por' => null,
                'atendido_en'  => null,
            ]);
            if (!$id) {
                $_SESSION['toast_error'] = 'No se pudo registrar la incidencia.';
                $this->redirect('/home/internet');
                return;
            }
            $_SESSION['toast_ok'] = 'Incidencia registrada correctamente. Código #' . $id;
            $this->redirect('/home/internet');
            return;
        }
        $this->view('home/internet', [
            'user'      => $user,
            'servicios' => $servicios,
            'internet'  => $internet
        ]);
    }


    public function luz(): void
    {
        require_once __DIR__ . '/../core/Auth.php';
        Auth::requireLogin();
        require_once __DIR__ . '/../models/Catalogo.php';
        require_once __DIR__ . '/../models/Luz.php';
        require_once __DIR__ . '/../models/Incidente.php';
        $user = Auth::user() ?? [];
        $servicios = Catalogo::servicios();
        $luz       = Luz::listar();
        $isPost = (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST');
        if ($isPost) {
            date_default_timezone_set('America/Lima');
            $oficina      = trim((string)$this->input('Oficina', ''));
            $tipoproblema = trim((string)$this->input('TipoProblema', ''));
            $descripcion  = trim((string)$this->input('Descripcion', ''));
            $telefono     = preg_replace('/\D+/', '', (string)$this->input('Telefono', ''));
            $errores = [];
            if ($oficina === '')      $errores[] = 'Seleccione la oficina.';
            if ($tipoproblema === '') $errores[] = 'Seleccione el tipo de problema.';
            if ($descripcion === '' || mb_strlen($descripcion) < 5) $errores[] = 'Ingrese una descripción (mín. 5 caracteres).';
            if ($telefono === '' || strlen($telefono) !== 9) $errores[] = 'Ingrese un teléfono válido de 9 dígitos.';
            $rutaFoto = null;
            if (!isset($_FILES['Foto']) || $_FILES['Foto']['error'] === UPLOAD_ERR_NO_FILE) {
                $errores[] = 'Seleccione una foto.';
            } else {
                if ($_FILES['Foto']['error'] !== UPLOAD_ERR_OK) {
                    $errores[] = 'No se pudo subir la foto.';
                } else {
                    $maxMB = 5;
                    if ((int)$_FILES['Foto']['size'] > $maxMB * 1024 * 1024) {
                        $errores[] = "La foto supera ${maxMB}MB.";
                    }
                    $ext = strtolower(pathinfo((string)$_FILES['Foto']['name'], PATHINFO_EXTENSION));
                    $permitidas = ['jpg', 'jpeg', 'png', 'webp'];
                    if (!in_array($ext, $permitidas, true)) {
                        $errores[] = 'Formato de foto no permitido (jpg, png, webp).';
                    }
                    if (empty($errores)) {
                        $dir = dirname(__DIR__, 2) . '/public/uploads/incidentes';
                        if (!is_dir($dir)) mkdir($dir, 0777, true);
                        $nombre  = 'inc_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                        $destino = $dir . '/' . $nombre;
                        if (!move_uploaded_file($_FILES['Foto']['tmp_name'], $destino)) {
                            $errores[] = 'No se pudo guardar la foto.';
                        } else {
                            $rutaFoto = '/uploads/incidentes/' . $nombre;
                        }
                    }
                }
            }
            if (!empty($errores)) {
                $_SESSION['toast_error'] = implode('<br>', array_map('htmlspecialchars', $errores));
                $this->redirect('/home/luz');
                return;
            }
            $usuarioNombre = trim(($user['Dni'] ?? 'Usuario'));
            if ($usuarioNombre === '') $usuarioNombre = 'Usuario';
            $usuarioId = (int)($user['id'] ?? 0);
            $id = Incidente::crearDesdeComputadora([
                'usuario'      => $usuarioNombre,
                'oficina'      => $oficina,
                'tipoproblema' => $tipoproblema,
                'descripcion'  => $descripcion,
                'foto'         => $rutaFoto,
                'telefono'     => $telefono,
                'fecha'        => date('Y-m-d'),
                'hora'         => date('H:i:s'),
                'estado'       => 'Pendiente',
                'atendido'     => null,
                'usuario_id'   => $usuarioId,
                'atendido_por' => null,
                'atendido_en'  => null,
            ]);
            if (!$id) {
                $_SESSION['toast_error'] = 'No se pudo registrar la incidencia.';
                $this->redirect('/home/luz');
                return;
            }
            $_SESSION['toast_ok'] = 'Incidencia registrada correctamente. Código #' . $id;
            $this->redirect('/home/luz');
            return;
        }
        $this->view('home/luz', [
            'user'      => $user,
            'servicios' => $servicios,
            'luz'       => $luz
        ]);
    }


    public function telefono(): void
    {
        require_once __DIR__ . '/../core/Auth.php';
        Auth::requireLogin();
        require_once __DIR__ . '/../models/Catalogo.php';
        require_once __DIR__ . '/../models/Telefono.php';
        require_once __DIR__ . '/../models/Incidente.php';
        $user = Auth::user() ?? [];
        $servicios = Catalogo::servicios();
        $telefono  = Telefono::listar();
        $isPost = (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST');
        if ($isPost) {
            date_default_timezone_set('America/Lima');
            $oficina      = trim((string)$this->input('Oficina', ''));
            $tipoproblema = trim((string)$this->input('TipoProblema', ''));
            $descripcion  = trim((string)$this->input('Descripcion', ''));
            $telefonoNum  = preg_replace('/\D+/', '', (string)$this->input('Telefono', ''));
            $errores = [];
            if ($oficina === '')      $errores[] = 'Seleccione la oficina.';
            if ($tipoproblema === '') $errores[] = 'Seleccione el tipo de problema.';
            if ($descripcion === '' || mb_strlen($descripcion) < 5) $errores[] = 'Ingrese una descripción (mín. 5 caracteres).';
            if ($telefonoNum === '' || strlen($telefonoNum) !== 9) $errores[] = 'Ingrese un teléfono válido de 9 dígitos.';
            $rutaFoto = null;
            if (!isset($_FILES['Foto']) || $_FILES['Foto']['error'] === UPLOAD_ERR_NO_FILE) {
                $errores[] = 'Seleccione una foto.';
            } else {
                if ($_FILES['Foto']['error'] !== UPLOAD_ERR_OK) {
                    $errores[] = 'No se pudo subir la foto.';
                } else {
                    $maxMB = 5;
                    if ((int)$_FILES['Foto']['size'] > $maxMB * 1024 * 1024) {
                        $errores[] = "La foto supera ${maxMB}MB.";
                    }
                    $ext = strtolower(pathinfo((string)$_FILES['Foto']['name'], PATHINFO_EXTENSION));
                    $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

                    if (!in_array($ext, $permitidas, true)) {
                        $errores[] = 'Formato de foto no permitido (jpg, png, webp).';
                    }
                    if (empty($errores)) {
                        $dir = dirname(__DIR__, 2) . '/public/uploads/incidentes';
                        if (!is_dir($dir)) mkdir($dir, 0777, true);

                        $nombre  = 'inc_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                        $destino = $dir . '/' . $nombre;

                        if (!move_uploaded_file($_FILES['Foto']['tmp_name'], $destino)) {
                            $errores[] = 'No se pudo guardar la foto.';
                        } else {
                            $rutaFoto = '/uploads/incidentes/' . $nombre;
                        }
                    }
                }
            }
            if (!empty($errores)) {
                $_SESSION['toast_error'] = implode('<br>', array_map('htmlspecialchars', $errores));
                $this->redirect('/home/telefono');
                return;
            }
            $usuarioNombre = trim(($user['Dni'] ?? 'Usuario'));
            if ($usuarioNombre === '') $usuarioNombre = 'Usuario';
            $usuarioId = (int)($user['id'] ?? 0);
            $id = Incidente::crearDesdeComputadora([
                'usuario'      => $usuarioNombre,
                'oficina'      => $oficina,
                'tipoproblema' => $tipoproblema,
                'descripcion'  => $descripcion,
                'foto'         => $rutaFoto,
                'telefono'     => $telefonoNum,
                'fecha'        => date('Y-m-d'),
                'hora'         => date('H:i:s'),
                'estado'       => 'Pendiente',
                'atendido'     => null,
                'usuario_id'   => $usuarioId,
                'atendido_por' => null,
                'atendido_en'  => null,
            ]);
            if (!$id) {
                $_SESSION['toast_error'] = 'No se pudo registrar la incidencia.';
                $this->redirect('/home/telefono');
                return;
            }
            $_SESSION['toast_ok'] = 'Incidencia registrada correctamente. Código #' . $id;
            $this->redirect('/home/telefono');
            return;
        }
        $this->view('home/telefono', [
            'user'      => $user,
            'servicios' => $servicios,
            'telefono'  => $telefono
        ]);
    }


    public function otros(): void
    {
        require_once __DIR__ . '/../core/Auth.php';
        Auth::requireLogin();
        require_once __DIR__ . '/../models/Catalogo.php';
        require_once __DIR__ . '/../models/Otros.php';
        require_once __DIR__ . '/../models/Incidente.php';
        $user = Auth::user() ?? [];
        $servicios = Catalogo::servicios();
        $otros     = Otros::listar();
        $isPost = (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST');
        if ($isPost) {
            date_default_timezone_set('America/Lima');
            $oficina      = trim((string)$this->input('Oficina', ''));
            $tipoproblema = trim((string)$this->input('TipoProblema', ''));
            $descripcion  = trim((string)$this->input('Descripcion', ''));
            $telefonoNum  = preg_replace('/\D+/', '', (string)$this->input('Telefono', ''));
            $errores = [];
            if ($oficina === '')      $errores[] = 'Seleccione la oficina.';
            if ($tipoproblema === '') $errores[] = 'Seleccione el tipo de problema.';
            if ($descripcion === '' || mb_strlen($descripcion) < 5) $errores[] = 'Ingrese una descripción (mín. 5 caracteres).';
            if ($telefonoNum === '' || strlen($telefonoNum) !== 9) $errores[] = 'Ingrese un teléfono válido de 9 dígitos.';
            $rutaFoto = null;
            if (!isset($_FILES['Foto']) || $_FILES['Foto']['error'] === UPLOAD_ERR_NO_FILE) {
                $errores[] = 'Seleccione una foto.';
            } else {
                if ($_FILES['Foto']['error'] !== UPLOAD_ERR_OK) {
                    $errores[] = 'No se pudo subir la foto.';
                } else {
                    $maxMB = 5;
                    if ((int)$_FILES['Foto']['size'] > $maxMB * 1024 * 1024) {
                        $errores[] = "La foto supera ${maxMB}MB.";
                    }
                    $ext = strtolower(pathinfo((string)$_FILES['Foto']['name'], PATHINFO_EXTENSION));
                    $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

                    if (!in_array($ext, $permitidas, true)) {
                        $errores[] = 'Formato de foto no permitido (jpg, png, webp).';
                    }
                    if (empty($errores)) {
                        $dir = dirname(__DIR__, 2) . '/public/uploads/incidentes';
                        if (!is_dir($dir)) mkdir($dir, 0777, true);

                        $nombre  = 'inc_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                        $destino = $dir . '/' . $nombre;

                        if (!move_uploaded_file($_FILES['Foto']['tmp_name'], $destino)) {
                            $errores[] = 'No se pudo guardar la foto.';
                        } else {
                            $rutaFoto = '/uploads/incidentes/' . $nombre;
                        }
                    }
                }
            }
            if (!empty($errores)) {
                $_SESSION['toast_error'] = implode('<br>', array_map('htmlspecialchars', $errores));
                $this->redirect('/home/otros');
                return;
            }
            $usuarioNombre = trim(($user['Dni'] ?? 'Usuario'));
            if ($usuarioNombre === '') $usuarioNombre = 'Usuario';
            $usuarioId = (int)($user['id'] ?? 0);
            $id = Incidente::crearDesdeComputadora([
                'usuario'      => $usuarioNombre,
                'oficina'      => $oficina,
                'tipoproblema' => $tipoproblema,
                'descripcion'  => $descripcion,
                'foto'         => $rutaFoto,
                'telefono'     => $telefonoNum,
                'fecha'        => date('Y-m-d'),
                'hora'         => date('H:i:s'),
                'estado'       => 'Pendiente',
                'atendido'     => null,
                'usuario_id'   => $usuarioId,
                'atendido_por' => null,
                'atendido_en'  => null,
            ]);
            if (!$id) {
                $_SESSION['toast_error'] = 'No se pudo registrar la incidencia.';
                $this->redirect('/home/otros');
                return;
            }
            $_SESSION['toast_ok'] = 'Incidencia registrada correctamente. Código #' . $id;
            $this->redirect('/home/otros');
            return;
        }
        $this->view('home/otros', [
            'user'      => $user,
            'servicios' => $servicios,
            'otros'     => $otros
        ]);
    }


    public function historial(): void
    {
        require_once __DIR__ . '/../core/Auth.php';
        Auth::requireLogin();
        require_once __DIR__ . '/../models/Incidente.php';
        $user = Auth::user() ?? [];
        $usuarioId = (int)($user['id'] ?? 0);
        $incidencias = [];
        if ($usuarioId > 0) {
            $incidencias = Incidente::byUserSemanaActual($usuarioId);
        }
        $this->view('home/historial', [
            'user' => $user,
            'incidencias' => $incidencias
        ]);
    }


    public function historialUpdates(): void
    {
        require_once __DIR__ . '/../core/Auth.php';
        Auth::requireLogin();
        require_once __DIR__ . '/../models/Incidente.php';
        $user = Auth::user() ?? [];
        $usuarioId = (int)($user['id'] ?? 0);
        date_default_timezone_set('America/Lima');
        $since = trim((string)($_GET['since'] ?? ''));
        if ($since === '') {
            $since = date('Y-m-d H:i:s', time() - 60);
        }
        $changes = [];
        if ($usuarioId > 0) {
            $changes = Incidente::cambiosSemanaActual($usuarioId, $since);
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok' => true,
            'serverTime' => date('Y-m-d H:i:s'),
            'changes' => $changes
        ]);
        exit;
    }


    public function opinion(): void
    {
        require_once __DIR__ . '/../core/Auth.php';
        Auth::requireLogin();
        require_once __DIR__ . '/../models/Opinion.php';
        $user = Auth::user() ?? [];
        $isPost = (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST');
        if ($isPost) {
            date_default_timezone_set('America/Lima');
            $descripcion = trim((string)($this->input('Descripcion', '')));
            $errores = [];
            if ($descripcion === '' || mb_strlen($descripcion) < 5) {
                $errores[] = 'Ingrese una descripción (mín. 5 caracteres).';
            }
            if (!empty($errores)) {
                $_SESSION['toast_error'] = implode('<br>', array_map('htmlspecialchars', $errores));
                $this->redirect('/home/opinion');
                return;
            }
            $usuario = trim((string)($user['Dni'] ?? 'Usuario'));
            if ($usuario === '') $usuario = 'Usuario';
            $ok = Opinion::crear([
                'usuario'      => $usuario,
                'descripcion'  => $descripcion,
                'fecha'        => date('Y-m-d'),
                'hora'         => date('H:i:s'),
            ]);
            if (!$ok) {
                $_SESSION['toast_error'] = 'No se pudo registrar tu opinión. Intente nuevamente.';
                $this->redirect('/home/opinion');
                return;
            }
            $_SESSION['toast_ok'] = '¡Gracias! Tu opinión fue registrada.';
            $this->redirect('/home/opinion');
            return;
        }
        $this->view('home/opinion', [
            'user' => $user
        ]);
    }


    public function cuenta(): void
    {
        require_once __DIR__ . '/../core/Auth.php';
        Auth::requireLogin();
        $user = Auth::user() ?? [];
        $this->view('home/cuenta', ['user' => $user]);
    }


    public function usuarios(): void
    {
        require_once __DIR__ . '/../core/Auth.php';
        Auth::requireLogin();
        $user = Auth::user() ?? [];
        $this->view('admin/usuarios', ['user' => $user]);
    }


    public function veropinion(): void
    {
        require_once __DIR__ . '/../core/Auth.php';
        Auth::requireLogin();
        require_once __DIR__ . '/../models/Opinion.php';
        $user = Auth::user() ?? [];
        $items = Opinion::listarConUsuarios();
        $this->view('home/veropinion', [
            'user'  => $user,
            'items' => $items
        ]);
    }


    public function datospersonales()
    {
        require_once __DIR__ . '/../core/Auth.php';
        require_once __DIR__ . '/../models/Usuario.php';
        Auth::requireLogin();
        $isAjax = isset($_GET['ajax']) && $_GET['ajax'] == '1';
        $authUser = Auth::user() ?? [];
        $userId = (int)($authUser['id'] ?? 0);
        $u = $userId ? Usuario::findById($userId) : null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $telefono = trim((string)($_POST['Telefono'] ?? $_POST['telefono'] ?? ''));
            $telefono = preg_replace('/\D+/', '', $telefono);
            if ($telefono === '') {
                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['ok' => false, 'message' => 'El teléfono es obligatorio.']);
                    exit;
                }
                $_SESSION['toast_error'] = 'El teléfono es obligatorio.';
                $this->redirect('/home/datospersonales');
                return;
            }
            if (strlen($telefono) < 9) {
                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['ok' => false, 'message' => 'Ingrese un teléfono válido.']);
                    exit;
                }
                $_SESSION['toast_error'] = 'Ingrese un teléfono válido.';
                $this->redirect('/home/datospersonales');
                return;
            }
            if (!$userId) {
                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['ok' => false, 'message' => 'Usuario no válido.']);
                    exit;
                }
                $_SESSION['toast_error'] = 'Usuario no válido.';
                $this->redirect('/home/datospersonales');
                return;
            }
            $ok = Usuario::updateTelefonoById($userId, $telefono);
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'ok' => (bool)$ok,
                    'message' => $ok ? 'Teléfono actualizado correctamente.' : 'No se pudo actualizar el teléfono.'
                ]);
                exit;
            }
            if ($ok) {
                $_SESSION['toast_ok'] = 'Teléfono actualizado correctamente.';
                if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
                    $_SESSION['user']['Telefono'] = $telefono;
                    $_SESSION['user']['telefono'] = $telefono;
                }
            } else {
                $_SESSION['toast_error'] = 'No se pudo actualizar el teléfono.';
            }
            $this->redirect('/home/datospersonales');
            return;
        }
        $this->view('home/datospersonales', [
            'u' => $u,
        ]);
    }



    public function cambiarcontrasena(): void
    {
        require_once __DIR__ . '/../core/Auth.php';
        Auth::requireLogin();
        $isPost = (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST');
        $isAjax = isset($_GET['ajax']);
        $msg = '';
        $ok  = false;
        if ($isPost) {
            require_once __DIR__ . '/../models/Usuario.php';
            $user = Auth::user() ?? [];
            $id = (int)($user['id'] ?? 0);
            $actual  = trim((string)($this->input('ActualPassword', '')));
            $pass    = trim((string)($this->input('Password', '')));
            $repite  = trim((string)($this->input('RepetirPassword', '')));
            if ($id <= 0) {
                $msg = 'Sesión inválida.';
            } elseif ($actual === '' || $pass === '' || $repite === '') {
                $msg = 'Completa todos los campos.';
            } elseif ($pass !== $repite) {
                $msg = 'La nueva contraseña no coincide.';
            } elseif (strlen($pass) < 6) {
                $msg = 'La nueva contraseña debe tener mínimo 6 caracteres.';
            } else {
                $hash = Usuario::getPasswordHashById($id);

                if (!$hash || !password_verify($actual, $hash)) {
                    $msg = 'La contraseña actual es incorrecta.';
                } elseif (password_verify($pass, $hash)) {
                    $msg = 'La nueva contraseña no puede ser igual a la actual.';
                } else {
                    $newHash = password_hash($pass, PASSWORD_DEFAULT);
                    $ok = Usuario::updatePasswordHash($id, $newHash);

                    $msg = $ok ? 'Contraseña actualizada correctamente.' : 'No se pudo actualizar. Intenta nuevamente.';
                }
            }
        }
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($ok ? 200 : 400);
            echo json_encode(['ok' => $ok, 'message' => $msg], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $this->view('home/cambiarcontrasena');
    }
}
