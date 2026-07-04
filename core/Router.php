<?php
/**
 * =========================================
 * SIG-GCM — Sistema Integrado de Gestão
 * Guarda Civil Municipal
 * =========================================
 * SISTEMA DE TESTE — Projeto acadêmico (TG/TC)
 * Desenvolvido 100% por:
 * Autor   : Rafaela Cristina Alcântara da Silva
 * GitHub  : https://github.com/rafaela13c
 * LinkedIn: https://www.linkedin.com/in/rafaela-cas1308/
 * =========================================
 * Este sistema NÃO é oficial e NÃO será
 * utilizado pela GCM. Uso exclusivo para
 * fins de estudo acadêmico.
 * =========================================
 */// =============================================
// SIG-GCM — Router
// =============================================

class Router {
    private array $routes = [];

    public function add(string $method, string $pattern, string $controller, string $action): void {
        $this->routes[] = compact('method', 'pattern', 'controller', 'action');
    }

    public function get(string $pattern, string $controller, string $action): void {
        $this->add('GET', $pattern, $controller, $action);
    }

    public function post(string $pattern, string $controller, string $action): void {
        $this->add('POST', $pattern, $controller, $action);
    }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $url    = trim($_GET['url'] ?? '', '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method && $route['method'] !== 'ANY') continue;

            // Converter padrão para regex: {id} → (\d+), {slug} → ([a-z0-9\-]+)
            $pattern = preg_replace('/\{id\}/', '(\d+)', $route['pattern']);
            $pattern = preg_replace('/\{[a-z_]+\}/', '([^/]+)', $pattern);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches); // Remover match completo
                $controllerFile = ROOT_PATH . '/controllers/' . $route['controller'] . '.php';

                if (!file_exists($controllerFile)) {
                    $this->notFound();
                    return;
                }

                require_once $controllerFile;
                $ctrl = new $route['controller']();
                $ctrl->{$route['action']}(...$matches);
                return;
            }
        }

        $this->notFound();
    }

    private function notFound(): void {
        http_response_code(404);
        if (file_exists(ROOT_PATH . '/views/errors/404.php')) {
            include ROOT_PATH . '/views/errors/404.php';
        } else {
            echo '<h1>404 — Página não encontrada</h1>';
        }
    }
}
