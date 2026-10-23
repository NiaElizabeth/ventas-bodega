<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\CsrfGuard;
use App\Core\ViewRenderer;
use App\Repositories\ProductoRepository;
use App\Repositories\VentaRepository;
use App\Services\VentaService;
use DateTimeImmutable;

final class VentaController
{
    public function __construct(
        private readonly ViewRenderer $views,
        private readonly CsrfGuard $csrf,
        private readonly VentaService $service,
        private readonly ProductoRepository $productos,
        private readonly VentaRepository $ventas
    ) {
    }

    public function index(): void
    {
        $today = new DateTimeImmutable('today');
        $this->views->render('ventas/index', [
            'ventas' => $this->ventas->listarVentasDelDia($today),
            'totalCentavos' => $this->ventas->totalVendidoDelDia($today),
        ]);
    }

    public function create(): void
    {
        $this->views->render('ventas/create', [
            'productos' => $this->productos->listarTodos(),
            'token' => $this->csrf->token(),
        ]);
    }

    public function store(): void
    {
        $this->csrf->verificar($_POST['_csrf'] ?? null);
        $result = $this->service->registrar($_POST);
        if (!$result->exitoso) {
            $this->views->render('ventas/create', [
                'productos' => $this->productos->listarTodos(),
                'token' => $this->csrf->token(),
                'errores' => $result->errores,
                'datos' => $_POST,
            ], 422);
            return;
        }

        $_SESSION['flash_message'] = 'Venta registrada correctamente.';
        header('Location: index.php?route=' . rawurlencode('/ventas'), true, 303);
    }
}
