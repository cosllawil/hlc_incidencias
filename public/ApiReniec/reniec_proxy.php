<?php
require_once __DIR__ . '/../../app/config/env.php';
require_once __DIR__ . '/../../app/config/config.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
$dni = preg_replace('/\D+/', '', (string)($_GET['dni'] ?? ''));
if ($dni === '' || strlen($dni) !== 8) {
  http_response_code(400);
  echo json_encode([
    'ok' => false,
    'error' => 'DNI inválido. Debe tener 8 dígitos.'
  ], JSON_UNESCAPED_UNICODE);
  exit;
}
if (!defined('RENIEC_TOKEN') || RENIEC_TOKEN === '') {
  http_response_code(500);
  echo json_encode([
    'ok' => false,
    'error' => 'Falta configurar RENIEC_TOKEN en app/config/config.php'
  ], JSON_UNESCAPED_UNICODE);
  exit;
}
$endpoint = 'https://dniruc.apisperu.com/api/v1/dni/' . $dni . '?token=' . urlencode(RENIEC_TOKEN);
$ch = curl_init($endpoint);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CONNECTTIMEOUT => 8,
  CURLOPT_TIMEOUT => 12,
  CURLOPT_SSL_VERIFYPEER => true,
  CURLOPT_HTTPHEADER => [
    'Accept: application/json'
  ],
]);

$body = curl_exec($ch);
$err  = curl_error($ch);
$code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($body === false) {
  http_response_code(502);
  echo json_encode([
    'ok' => false,
    'error' => 'Error de conexión al servicio RENIEC: ' . $err
  ], JSON_UNESCAPED_UNICODE);
  exit;
}
$data = json_decode($body, true);
if ($code !== 200 || !is_array($data) || empty($data['nombres'])) {
  http_response_code(200);
  echo json_encode([
    'ok' => false,
    'error' => $data['message'] ?? 'No encontrado'
  ], JSON_UNESCAPED_UNICODE);
  exit;
}
http_response_code(200);
echo json_encode([
  'ok' => true,
  'dni' => $dni,
  'nombres' => $data['nombres'] ?? '',
  'apellidoPaterno' => $data['apellidoPaterno'] ?? '',
  'apellidoMaterno' => $data['apellidoMaterno'] ?? '',
], JSON_UNESCAPED_UNICODE);
