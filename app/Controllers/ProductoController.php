<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\CsrfGuard;
use App\Core\ViewRenderer;
use App\Repositories\ProductoRepository;
use App\Services\ProductoService;

final class ProductoController
{
    public function __construct(
        private readonly ViewRenderer $views,
        private readonly CsrfGuard $csrf,
        private readonly ProductoService $service,
        private readonly ProductoRepository $productos
    ) {
    }

    public function index(): void
    {
        $this->views->render('productos/index', ['productos' => $this->productos->listarTodos()]);
    }

    public function create(): void
    {
        $this->views->render('productos/create', ['token' => $this->csrf->token()]);
    }

    public function store(): void
    {
        $this->csrf->verificar($_POST['_csrf'] ?? null);
        $result = $this->service->registrar($_POST);
        if (!$result->exitoso) {
            $this->views->render('productos/create', [
                'token' => $this->csrf->token(),
                'errores' => $result->errores,
                'datos' => $_POST,
            ], 422);
            return;
        }

        $this->redirect('/productos', 'Producto registrado correctamente.');
    }

    private function redirect(string $route, string $message): void
    {
        $_SESSION['flash_message'] = $message;
        header('Location: index.php?route=' . rawurlencode($route), true, 303);
    }
}
