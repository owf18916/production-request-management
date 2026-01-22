<?php

namespace App;

/**
 * Route Class
 * Represents a single route
 */
class Route
{
    private string $method;
    private string $path;
    private string|array $action;
    private array $middleware = [];
    private array $params = [];

    public function __construct(string $method, string $path, string|array $action)
    {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->action = $action;
    }

    /**
     * Get the route method
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get the route path
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get the route action
     */
    public function getAction(): string|array
    {
        return $this->action;
    }

    /**
     * Add middleware to route
     */
    public function middleware(array $middleware): self
    {
        $this->middleware = $middleware;
        return $this;
    }

    /**
     * Get route middleware
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * Set route parameters
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * Get route parameters
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Match the route against a path
     */
    public function matches(string $method, string $path): bool
    {
        if ($this->method !== $method) {
            return false;
        }

        return $this->matchPath($path);
    }

    /**
     * Match the path
     */
    private function matchPath(string $path): bool
    {
        // First, check for exact static match (for literal paths)
        if ($this->path === $path) {
            return true;
        }

        // Only apply regex if route contains parameters
        if (strpos($this->path, '{') === false) {
            return false;
        }

        $pattern = preg_replace('/{([a-zA-Z_][a-zA-Z0-9_]*)}/', '(?P<$1>[^/]+)', $this->path);
        $pattern = "#^{$pattern}$#";

        if (preg_match($pattern, $path, $matches)) {
            $params = array_filter($matches, fn($key) => is_string($key), ARRAY_FILTER_USE_KEY);
            $this->setParams($params);
            return true;
        }

        return false;
    }
}
