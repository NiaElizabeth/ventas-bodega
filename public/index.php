<?php
declare(strict_types=1);

use App\Core\CsrfGuard;
use App\Core\Router;
use App\Core\Session;
use App\Core\ViewRenderer;
use App\Exceptions\ProductoNoEncontradoException;
use App\Exceptions\StockInsuficienteException;
use App\Exceptions\TokenCsrfInvalidoException;
use App\Controllers\DashboardController;
use App\Controllers\ProductoController;
use App\Controllers\VentaController;
use App\Repositories\ProductoRepository;
use App\Repositories\VentaRepository;
use App\Services\ProductoService;
use App\Services\VentaService;

require dirname(__DIR__) . '/app/autoload.php';

try {
    Session::start();

    $pdo = require dirname(__DIR__) . '/config/database.php';
    $productoRepository = new ProductoRepository($pdo);
    $ventaRepository = new VentaRepository($pdo);
    $productoService = new ProductoService($productoRepository);
    $ventaService = new VentaService($pdo, $productoRepository, $ventaRepository);
    $views = new ViewRenderer(dirname(__DIR__) . '/views');
    $csrf = new CsrfGuard();

    $router = new Router(
        new DashboardController($views),
        new ProductoController($views, $csrf, $productoService, $productoRepository),
        new VentaController($views, $csrf, $ventaService, $productoRepository, $ventaRepository)
    );
    $router->dispatch(
        strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
        (string) ($_GET['route'] ?? '/')
    );
} catch (TokenCsrfInvalidoException $exception) {
    http_response_code(422);
    error_log($exception->getMessage());
    echo 'Petición no válida. Recargue el formulario e inténtelo nuevamente.';
} catch (StockInsuficienteException $exception) {
    http_response_code(409);
    error_log($exception->getMessage());
    echo 'No hay stock suficiente para completar la venta.';
} catch (ProductoNoEncontradoException $exception) {
    http_response_code(404);
    error_log($exception->getMessage());
    echo 'El producto solicitado no está registrado.';
} catch (Throwable $exception) {
    http_response_code(500);
    error_log((string) $exception);
    echo 'No se pudo completar la solicitud. Revise la configuración o los registros del sistema.';
}
