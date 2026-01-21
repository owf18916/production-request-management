<?php

namespace App;

/**
 * Router Class
 * Handles routing and request dispatching
 */
class Router
{
    private array $routes = [];
    private ?Route $currentRoute = null;

    /**
     * Register a GET route
     */
    public function get(string $path, string|array $action): Route
    {
        return $this->addRoute('GET', $path, $action);
    }

    /**
     * Register a POST route
     */
    public function post(string $path, string|array $action): Route
    {
        return $this->addRoute('POST', $path, $action);
    }

    /**
     * Register a PUT route
     */
    public function put(string $path, string|array $action): Route
    {
        return $this->addRoute('PUT', $path, $action);
    }

    /**
     * Register a DELETE route
     */
    public function delete(string $path, string|array $action): Route
    {
        return $this->addRoute('DELETE', $path, $action);
    }

    /**
     * Register a PATCH route
     */
    public function patch(string $path, string|array $action): Route
    {
        return $this->addRoute('PATCH', $path, $action);
    }

    /**
     * Register any method route
     */
    public function any(string $path, string|array $action): Route
    {
        $this->addRoute('GET', $path, $action);
        return $this->addRoute('POST', $path, $action);
    }

    /**
     * Add a route
     */
    private function addRoute(string $method, string $path, string|array $action): Route
    {
        $route = new Route($method, $path, $action);
        $this->routes[] = $route;
        $this->currentRoute = $route;
        return $route;
    }

    /**
     * Dispatch a request
     */
    public function dispatch(string $method, string $path): void
    {
        foreach ($this->routes as $route) {
            if ($route->matches($method, $path)) {
                $this->handleRoute($route);
                return;
            }
        }

        http_response_code(404);
        die('404 - Route not found');
    }

    /**
     * Handle route execution
     */
    private function handleRoute(Route $route): void
    {
        $action = $route->getAction();

        if (is_string($action)) {
            [$controller, $method] = explode('@', $action);
            $this->callController($controller, $method, $route);
        } elseif (is_callable($action)) {
            $action($route->getParams());
        }
    }

    /**
     * Call a controller method
     */
    private function callController(string $controller, string $method, Route $route): void
    {
        $controllerClass = "App\\Controllers\\$controller";

        if (!class_exists($controllerClass)) {
            http_response_code(404);
            die("Controller not found: $controllerClass");
        }

        $instance = new $controllerClass();

        if (!method_exists($instance, $method)) {
            http_response_code(404);
            die("Method not found: $method in $controllerClass");
        }

        $instance->$method(...array_values($route->getParams()));
    }

    /**
     * Get all registered routes
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
