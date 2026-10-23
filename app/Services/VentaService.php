<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ProductoNoEncontradoException;
use App\Exceptions\StockInsuficienteException;
use App\Models\ResultadoOperacion;
use App\Models\Venta;
use App\Repositories\ProductoRepository;
use App\Repositories\VentaRepository;
use PDO;
use Throwable;

final class VentaService
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly ProductoRepository $productos,
        private readonly VentaRepository $ventas
    ) {
    }

    /** @param array<string, mixed> $datos */
    public function registrar(array $datos): ResultadoOperacion
    {
        if (!self::cantidadValida($datos['cantidad'] ?? null)) {
            return ResultadoOperacion::fallo(['La cantidad debe ser un entero mayor que cero.']);
        }
        if (!self::idValido($datos['producto_id'] ?? null)) {
            return ResultadoOperacion::fallo(['Seleccione un producto válido.']);
        }

        $productoId = (int) $datos['producto_id'];
        $cantidad = (int) $datos['cantidad'];

        $this->pdo->beginTransaction();
        try {
            $producto = $this->productos->obtenerPorId($productoId, true);
            if ($producto === null) {
                throw new ProductoNoEncontradoException('Producto inexistente: ' . $productoId);
            }
            if (!self::hayStockSuficiente($cantidad, $producto->stockActual)) {
                throw new StockInsuficienteException('Stock insuficiente para el producto ' . $productoId);
            }

            $totalCentavos = self::calcularTotal($producto->precioVentaCentavos, $cantidad);
            $this->ventas->insertar($productoId, $cantidad, $producto->precioVentaCentavos, $totalCentavos);

            if ($this->productos->descontarStock($productoId, $cantidad) !== 1) {
                throw new StockInsuficienteException('El stock cambió durante el registro de la venta.');
            }

            $this->pdo->commit();
            return ResultadoOperacion::exito();
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    public static function cantidadValida(mixed $cantidad): bool
    {
        return (is_int($cantidad) || is_string($cantidad))
            && preg_match('/^[1-9]\d*$/D', (string) $cantidad) === 1
            && (float) $cantidad <= 4294967295;
    }

    public static function idValido(mixed $id): bool
    {
        return (is_int($id) || is_string($id))
            && preg_match('/^[1-9]\d*$/D', (string) $id) === 1
            && (float) $id <= 4294967295;
    }

    public static function hayStockSuficiente(int $cantidad, int $stockActual): bool
    {
        return $cantidad > 0 && $stockActual >= $cantidad;
    }

    public static function calcularTotal(int $precioCentavos, int $cantidad): int
    {
        if ($precioCentavos < 0 || $cantidad < 1 || $precioCentavos > intdiv(9999999999, $cantidad)) {
            throw new \InvalidArgumentException('El total excede el rango permitido.');
        }
        return $precioCentavos * $cantidad;
    }

    public static function debeResaltar(int $stockActual, int $stockMinimo): bool
    {
        return $stockActual <= $stockMinimo;
    }

    /** @param list<Venta> $ventas */
    public static function totalVendido(array $ventas): int
    {
        return array_sum(array_map(static fn (Venta $venta): int => $venta->totalCentavos, $ventas));
    }
}
