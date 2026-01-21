<?php

/**
 * PSR-4 Autoloader
 * Automatically loads classes based on namespaces
 */
class Autoloader
{
    /**
     * Register the autoloader
     */
    public static function register(): void
    {
        spl_autoload_register([self::class, 'autoload']);
    }

    /**
     * Autoload a class based on namespace
     */
    public static function autoload(string $class): void
    {
        $prefix = 'App\\';
        $base_dir = __DIR__ . '/app/';

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }
}
