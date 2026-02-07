<?php

class Controller
{
  protected function view(string $view, array $data = []): void
  {
    extract($data);
    $base = rtrim((string)(defined('BASE_URL') ? BASE_URL : ''), '/');
    $viewFile = __DIR__ . '/../views/' . trim($view, '/') . '.php';
    if (!file_exists($viewFile)) {
      http_response_code(500);
      echo "Vista no encontrada: {$viewFile}";
      return;
    }
    require $viewFile;
  }



  protected function url(string $path = ''): string
  {
    $base = rtrim((string)BASE_URL, '/');
    $path = '/' . ltrim($path, '/');
    return $base . $path;
  }



  protected function redirect(string $to): void
  {
    if (preg_match('#^https?://#i', $to)) {
      header('Location: ' . $to);
      exit;
    }
    header('Location: ' . $this->url($to));
    exit;
  }


  protected function isPost(): bool
  {
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
  }


  protected function input(string $key, $default = null)
  {
    return $_POST[$key] ?? $_GET[$key] ?? $default;
  }
}
