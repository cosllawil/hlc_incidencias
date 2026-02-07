<?php

class Router
{
  private array $routes = [];


  public function get(string $path, string $handler): void
  {
    $this->add('GET', $path, $handler);
  }


  public function post(string $path, string $handler): void
  {
    $this->add('POST', $path, $handler);
  }


  private function add(string $method, string $path, string $handler): void
  {
    $path = '/' . trim($path, '/');
    $this->routes[] = [$method, $path, $handler];
  }


  public function dispatch(string $method, string $uri): void
  {
    $path = parse_url($uri, PHP_URL_PATH) ?? '/';
    $base = rtrim(BASE_URL, '/');
    if ($base !== '' && str_starts_with($path, $base)) {
      $path = substr($path, strlen($base));
      $path = $path === '' ? '/' : $path;
    }
    $path = '/' . trim($path, '/');
    foreach ($this->routes as [$m, $p, $handler]) {
      if ($m !== $method) continue;
      if ($p !== $path) continue;
      [$controllerName, $action] = explode('@', $handler);
      $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';
      if (!file_exists($controllerFile)) {
        http_response_code(500);
        echo "Controller no encontrado: $controllerName";
        return;
      }
      require_once $controllerFile;
      if (!class_exists($controllerName)) {
        http_response_code(500);
        echo "Clase controller no encontrada: $controllerName";
        return;
      }
      $controller = new $controllerName();
      if (!method_exists($controller, $action)) {
        http_response_code(500);
        echo "Acción no encontrada: $controllerName@$action";
        return;
      }
      $controller->$action();
      return;
    }
    http_response_code(404);
    echo '404 - Página no encontrada';
  }
}
