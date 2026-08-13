<?php

class router {

    private array $routes = [];

    public function get(string $path, callable $handler): void {
        $this->routes['GET'][$this->normalizePath($path)] = $handler;
    }

    public function post(string $path, callable $handler): void {
        $this->routes['POST'][$this->normalizePath($path)] = $handler;
    }

    public function put(string $path, callable $handler): void {
        $this->routes['PUT'][$this->normalizePath($path)] = $handler;
    }

    public function delete(string $path, callable $handler): void {
        $this->routes['DELETE'][$this->normalizePath($path)] = $handler;
    }

    public function dispatch(string $requestMethod, string $requestUri): void {

        $method = strtoupper($requestMethod);
        $rawPath = parse_url($requestUri, PHP_URL_PATH) ?? '/';

        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

        if ($scriptDir !== '/' && $scriptDir !== '.') {
            $prefix = $scriptDir . '/';
            if (stripos($rawPath . '/', $prefix) === 0) {
                $rawPath = substr($rawPath, strlen($scriptDir));
            }
        }

        $path = $this->normalizePath($rawPath);

        if (isset($this->routes[$method][$path])) {
            call_user_func($this->routes[$method][$path]);
            return;
        }

        http_response_code(404);
        echo json_encode([
            "uri" =>$_SERVER['REQUEST_URI'],
            "script"=>$_SERVER['SCRIPT_NAME'],
            "error" => "Route not found",
            "requested_path" => $path,
            "method" => $method
        ]);
    }

    private function normalizePath(string $path): string {
        $trimmed = trim($path, '/');
        return $trimmed === '' ? '/' : '/' . $trimmed;
    }
}