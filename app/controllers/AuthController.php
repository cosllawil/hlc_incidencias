<?php

require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/models/Usuario.php';
require_once dirname(__DIR__) . '/core/Auth.php';

class AuthController extends Controller
{
    public function registerForm(): void
    {
        $flashError = $_SESSION['flash_error'] ?? '';
        $flashSuccess = $_SESSION['flash_success'] ?? '';
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);
        $this->view('auth/register', compact('flashError', 'flashSuccess'));
    }


    public function register(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/auth/register');
        }
        $tipo = trim($_POST['TipoDocumento'] ?? '');
        $dni  = preg_replace('/\D+/', '', $_POST['Dni'] ?? '');
        $nom  = trim($_POST['Nombres'] ?? '');
        $ape  = trim($_POST['Apellidos'] ?? '');
        $tel  = preg_replace('/\D+/', '', $_POST['Telefono'] ?? '');
        $pass = (string)($_POST['Password'] ?? '');
        $rep  = (string)($_POST['RepetirPassword'] ?? '');
        if ($tipo === '' || $dni === '' || $nom === '' || $ape === '' || $tel === '' || $pass === '' || $rep === '') {
            $_SESSION['flash_error'] = 'Complete todos los campos.';
            $this->redirect('/auth/register');
        }
        if ($pass !== $rep) {
            $_SESSION['flash_error'] = 'Las contraseñas no coinciden.';
            $this->redirect('/auth/register');
        }
        if (Usuario::existeDocumento($tipo, $dni)) {
            $_SESSION['flash_error'] = 'Este documento ya está registrado.';
            $this->redirect('/auth/register');
        }
        $ok = Usuario::crear([
            'TipoDocumento' => $tipo,
            'Dni'           => $dni,
            'Nombres'       => mb_strtoupper($nom, 'UTF-8'),
            'Apellidos'     => mb_strtoupper($ape, 'UTF-8'),
            'Telefono'      => $tel,
            'PasswordHash'  => password_hash($pass, PASSWORD_BCRYPT),
            'Rol'           => 'PERSONAL',
        ]);
        if (!$ok) {
            $_SESSION['flash_error'] = 'No se pudo registrar. Intente nuevamente.';
            $this->redirect('/auth/register');
        }
        $_SESSION['flash_success'] = 'Registro exitoso. Inicia sesión.';
        $this->redirect('/auth/login');
    }


    public function loginForm(): void
    {
        $flashError = $_SESSION['flash_error'] ?? '';
        $flashSuccess = $_SESSION['flash_success'] ?? '';
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);
        $this->view('auth/login', compact('flashError', 'flashSuccess'));
    }


    public function login(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/auth/login');
        }
        $dni  = preg_replace('/\D+/', '', $_POST['dni'] ?? '');
        $pass = (string)($_POST['password'] ?? '');

        if ($dni === '' || $pass === '') {
            $_SESSION['flash_error'] = 'Ingrese DNI y contraseña.';
            $this->redirect('/auth/login');
        }
        $user = Usuario::buscarPorDocumento($dni);
        if (!$user || !password_verify($pass, $user['PasswordHash'])) {
            $_SESSION['flash_error'] = 'Credenciales inválidas.';
            $this->redirect('/auth/login');
        }
        $_SESSION['user'] = [
            'id'        => (int)$user['id'],
            'Dni'       => $user['Dni'],
            'Nombres'   => $user['Nombres'],
            'Apellidos' => $user['Apellidos'],
            'Rol'       => $user['Rol'],
        ];
        if (strtoupper((string)$user['Rol']) === 'ADMIN') {
            $this->redirect('/admin/bandeja');
        } else {
            $this->redirect('/home/dashboard');
        }
    }


    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();

        header('Location: /hlc_incidencias/auth/login');
        exit;
    }


    public function reset(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dni = trim($_POST['Dni'] ?? '');
            if ($dni === '') {
                $_SESSION['flash_error'] = 'Ingrese el DNI';
                $this->redirect('/auth/reset');
            }
            $usuario = Usuario::findByDni($dni);
            if (!$usuario) {
                $_SESSION['flash_error'] = 'No existe el usuario';
                $this->redirect('/auth/reset');
            }
            $_SESSION['reset_user'] = [
                'id' => $usuario['id'],
                'Dni' => $usuario['Dni'],
                'Nombres' => $usuario['Nombres']
            ];
            $this->redirect('/auth/contrasena');
        }
        $this->view('auth/reset');
    }


    public function contrasena(): void
    {
        if (!isset($_SESSION['reset_user'])) {
            $_SESSION['flash_error'] = 'Acceso no permitido';
            $this->redirect('/auth/reset');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pass = $_POST['Password'] ?? '';
            $rep  = $_POST['RepetirPassword'] ?? '';
            if ($pass === '' || $rep === '') {
                $_SESSION['flash_error'] = 'Complete todos los campos';
                $this->redirect('/auth/contrasena');
            }
            if ($pass !== $rep) {
                $_SESSION['flash_error'] = 'Las contraseñas no coinciden';
                $this->redirect('/auth/contrasena');
            }
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            Usuario::actualizarPassword($_SESSION['reset_user']['id'], $hash);
            unset($_SESSION['reset_user']);
            $_SESSION['flash_success'] = 'Contraseña actualizada correctamente';
            $this->redirect('/auth/login');
        }
        $this->view('auth/contrasena', $_SESSION['reset_user']);
    }
}
