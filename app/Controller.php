<?php

namespace App;

/**
 * Base Controller Class
 * Provides common functionality for all controllers
 */
abstract class Controller
{
    /**
     * View data
     */
    protected array $data = [];

    /**
     * Page title
     */
    protected string $title = '';

    /**
     * Render a view
     */
    protected function view(string $view, array $data = [], bool $withLayout = true): void
    {
        $this->data = array_merge($this->data, $data);
        $viewPath = __DIR__ . "/Views/$view.php";

        if (!file_exists($viewPath)) {
            http_response_code(404);
            die("View not found: $view");
        }

        // Capture view output
        ob_start();
        extract($this->data);
        require $viewPath;
        $content = ob_get_clean();

        // Render with layout if needed
        if ($withLayout) {
            extract($this->data);
            $layoutPath = __DIR__ . "/Views/layouts/main.php";
            require $layoutPath;
        } else {
            echo $content;
        }
    }

    /**
     * Render JSON response
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect to a URL
     */
    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    /**
     * Get request method
     */
    protected function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Get request input
     */
    protected function input(string $key = null): mixed
    {
        $input = [];

        if ($this->method() === 'GET') {
            $input = $_GET;
        } elseif ($this->method() === 'POST') {
            $input = $_POST;
        }

        if ($key === null) {
            return $input;
        }

        return $input[$key] ?? null;
    }

    /**
     * Get all input
     */
    protected function all(): array
    {
        return $this->input();
    }

    /**
     * Check if input key exists
     */
    protected function has(string $key): bool
    {
        return isset($this->input()[$key]);
    }

    /**
     * Validate input
     */
    protected function validate(array $rules): array
    {
        $input = $this->input();
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $input[$field] ?? '';
            $ruleArray = explode('|', $rule);

            foreach ($ruleArray as $r) {
                $this->validateRule($field, $value, $r, $errors);
            }
        }

        return $errors;
    }

    /**
     * Validate individual rule
     */
    private function validateRule(string $field, mixed $value, string $rule, array &$errors): void
    {
        if (str_contains($rule, ':')) {
            [$rule, $param] = explode(':', $rule);
        }

        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    $errors[$field] = ucfirst($field) . ' is required';
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = ucfirst($field) . ' must be a valid email';
                }
                break;

            case 'min':
                if (strlen($value) < (int)$param) {
                    $errors[$field] = ucfirst($field) . ' must be at least ' . $param . ' characters';
                }
                break;

            case 'max':
                if (strlen($value) > (int)$param) {
                    $errors[$field] = ucfirst($field) . ' must not exceed ' . $param . ' characters';
                }
                break;

            case 'numeric':
                if (!is_numeric($value)) {
                    $errors[$field] = ucfirst($field) . ' must be numeric';
                }
                break;

            case 'confirmed':
                $confirmed = $_POST[$field . '_confirmation'] ?? '';
                if ($value !== $confirmed) {
                    $errors[$field] = ucfirst($field) . ' does not match';
                }
                break;
        }
    }

    /**
     * Set view data
     */
    protected function with(string $key, mixed $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Set title
     */
    protected function setTitle(string $title): self
    {
        $this->title = $title;
        $this->data['title'] = $title;
        return $this;
    }
}
