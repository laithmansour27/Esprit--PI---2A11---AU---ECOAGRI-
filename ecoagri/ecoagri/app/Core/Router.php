<?php
// Matches "METHOD /path" to [ControllerName, 'method'] from app/routes.php.
class Router
{
    public function __construct(private array $routes) {}

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';

        $base = base_path();
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }
        $path = '/' . trim(preg_replace('#^/index\.php#', '', $path), '/');
        if (isset($_GET['route']) && is_string($_GET['route'])) {
            $path = '/' . trim($_GET['route'], '/');
        }

        $handler = $this->routes["$method $path"] ?? null;
        if ($handler === null) {
            http_response_code(404);
            (new HomeController())->notFound();
            return;
        }

        [$class, $action] = $handler;
        (new $class())->$action();
    }
}
