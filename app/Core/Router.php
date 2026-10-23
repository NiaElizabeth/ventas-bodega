<?php
declare(strict_types=1);

namespace App\Core;

use App\Controllers\DashboardController;
use App\Controllers\ProductoController;
use App\Controllers\VentaController;

final class Router
{
    public function __construct(
        private readonly DashboardController $dashboard,
        private readonly ProductoController $productos,
        private readonly VentaController $ventas
    ) {
    }

    public function dispatch(string $method, string $route): void
    {
        if ($route === '/' && $method === 'GET') {
            $this->dashboard->index();
            return;
        }

        if ($route === '/productos' && $method === 'GET') {
            $this->productos->index();
            return;
        }

        if ($route === '/productos/crear' && $method === 'GET') {
            $this->productos->create();
            return;
        }

        if ($route === '/productos/guardar' && $method === 'POST') {
            $this->productos->store();
            return;
        }

        if ($route === '/ventas' && $method === 'GET') {
            $this->ventas->index();
            return;
        }

        if ($route === '/ventas/crear' && $method === 'GET') {
            $this->ventas->create();
            return;
        }

        if ($route === '/ventas/guardar' && $method === 'POST') {
            $this->ventas->store();
            return;
        }

        if ($route === '/productos/guardar' || $route === '/ventas/guardar') {
            http_response_code(405);
            header('Allow: POST');
            echo 'Método no permitido.';
            return;
        }

        http_response_code(404);
        echo 'Página no encontrada.';
    }
}
