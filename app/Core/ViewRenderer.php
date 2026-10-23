<?php
declare(strict_types=1);

namespace App\Core;

final class ViewRenderer
{
    public function __construct(private readonly string $directory)
    {
    }

    /** @param array<string, mixed> $data */
    public function render(string $template, array $data = [], int $status = 200): void
    {
        $templatePath = $this->resolve($template);
        http_response_code($status);

        $e = static fn (mixed $value): string => htmlspecialchars(
            (string) ($value ?? ''),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
        $url = static fn (string $route): string => 'index.php?route=' . rawurlencode($route);

        extract($data, EXTR_SKIP);
        ob_start();
        require $templatePath;
        $content = (string) ob_get_clean();

        $layoutPath = $this->resolve('layout.php');
        require $layoutPath;
    }

    private function resolve(string $template): string
    {
        $path = $this->directory . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $template);
        if (!str_ends_with($path, '.php')) {
            $path .= '.php';
        }
        if (!is_file($path)) {
            throw new \RuntimeException('No se encontró la vista solicitada.');
        }
        return $path;
    }
}
