<?php


declare(strict_types=1);
require_once __DIR__ . '/../app/config/env.php';
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Router.php';

session_start();
$router = new Router();
$router->get('/', 'HomeController@landing');
$router->get('/home/dashboard', 'HomeController@dashboard');
$router->get('/home/computadora', 'HomeController@computadora');
$router->post('/home/computadora', 'HomeController@computadora');
$router->get('/home/impresora', 'HomeController@impresora');
$router->post('/home/impresora', 'HomeController@impresora');
$router->get('/home/internet', 'HomeController@internet');
$router->post('/home/internet', 'HomeController@internet');
$router->get('/home/luz', 'HomeController@luz');
$router->post('/home/luz', 'HomeController@luz');
$router->get('/home/telefono', 'HomeController@telefono');
$router->post('/home/telefono', 'HomeController@telefono');
$router->get('/home/otros', 'HomeController@otros');
$router->post('/home/otros', 'HomeController@otros');
$router->get('/home/historial', 'HomeController@historial');
$router->post('/home/historial', 'HomeController@historial');
$router->get('/home/historial-updates', 'HomeController@historialUpdates');
$router->get('/home/opinion', 'HomeController@opinion');
$router->post('/home/opinion', 'HomeController@opinion');
$router->get('/admin/dashboard', 'AdminController@dashboard');
$router->get('/auth/login', 'AuthController@loginForm');
$router->post('/auth/login', 'AuthController@login');
$router->get('/auth/register', 'AuthController@registerForm');
$router->post('/auth/register', 'AuthController@register');
$router->get('/auth/logout', 'AuthController@logout');
$router->get('/admin/bandeja', 'AdminController@bandeja');
$router->post('/admin/estado', 'AdminController@cambiarEstado');
$router->get('/auth/reset', 'AuthController@reset');
$router->post('/auth/reset', 'AuthController@reset');
$router->get('/auth/contrasena', 'AuthController@contrasena');
$router->post('/auth/contrasena', 'AuthController@contrasena');
$router->get('/home/cuenta', 'HomeController@cuenta');
$router->get('/admin/usuarios', 'AdminController@usuarios');
$router->post('/admin/usuarios', 'AdminController@usuarios');
$router->get('/admin/veropinion', 'AdminController@veropinion');
$router->get('/admin/atender', 'AdminController@atender');
$router->post('/admin/atender', 'AdminController@atender');
$router->get('/admin/verdetalles', 'AdminController@verdetalles');
$router->get('/home/datospersonales', 'HomeController@datospersonales');
$router->post('/home/datospersonales', 'HomeController@datospersonales');
$router->get('/home/cambiarcontrasena', 'HomeController@cambiarcontrasena');
$router->post('/home/cambiarcontrasena', 'HomeController@cambiarcontrasena');

require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri    = $_SERVER['REQUEST_URI'] ?? '/';
$basePath = '/hlc_incidencias';
if (strpos($uri, $basePath) === 0) {
  $uri = substr($uri, strlen($basePath));
  if ($uri === '') $uri = '/';
}

$router->dispatch($method, $uri);


