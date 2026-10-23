<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Producto;
use App\Models\ResultadoOperacion;
use App\Repositories\ProductoRepository;
use PDOException;

final class ProductoService
{
    public function __construct(private readonly ProductoRepository $productos)
    {
    }

    /** @param array<string, mixed> $datos */
    public function registrar(array $datos): ResultadoOperacion
    {
        $errores = self::validar($datos);
        if ($errores !== []) {
            return ResultadoOperacion::fallo($errores);
        }

        $codigo = trim((string) $datos['codigo']);
        if ($this->productos->codigoExiste($codigo)) {
            return ResultadoOperacion::fallo(['El código del producto ya existe.']);
        }

        $producto = new Producto(
            null,
            $codigo,
            trim((string) $datos['nombre']),
            trim((string) ($datos['categoria'] ?? '')) === '' ? null : trim((string) $datos['categoria']),
            self::precioACentavos((string) $datos['precio_venta']),
            (int) $datos['stock_actual'],
            (int) $datos['stock_minimo']
        );

        try {
            $this->productos->insertar($producto);
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                return ResultadoOperacion::fallo(['El código del producto ya existe.']);
            }
            throw $exception;
        }

        return ResultadoOperacion::exito();
    }

    /** @param array<string, mixed> $datos
     *  @return list<string>
     */
    public static function validar(array $datos): array
    {
        $errores = [];
        $codigoValue = $datos['codigo'] ?? null;
        $nombreValue = $datos['nombre'] ?? null;
        $categoriaValue = $datos['categoria'] ?? null;
        $codigo = is_string($codigoValue) ? trim($codigoValue) : '';
        $nombre = is_string($nombreValue) ? trim($nombreValue) : '';

        if ($codigo === '') {
            $errores[] = 'El código es obligatorio.';
        } elseif (strlen($codigo) > 64) {
            $errores[] = 'El código no puede superar 64 caracteres.';
        }
        if ($nombre === '') {
            $errores[] = 'El nombre es obligatorio.';
        } elseif (strlen($nombre) > 150) {
            $errores[] = 'El nombre no puede superar 150 caracteres.';
        }
        if ($categoriaValue !== null && !is_string($categoriaValue)) {
            $errores[] = 'La categoría no es válida.';
        } elseif (is_string($categoriaValue) && strlen(trim($categoriaValue)) > 80) {
            $errores[] = 'La categoría no puede superar 80 caracteres.';
        }
        if (!self::precioValido($datos['precio_venta'] ?? null)) {
            $errores[] = 'El precio debe ser un número mayor o igual a cero con hasta dos decimales.';
        }
        foreach (['stock_actual' => 'Stock actual', 'stock_minimo' => 'Stock mínimo'] as $key => $label) {
            if (!self::enteroNoNegativo($datos[$key] ?? null)) {
                $errores[] = $label . ' debe ser un entero no negativo.';
            }
        }

        return $errores;
    }

    public static function precioValido(mixed $precio): bool
    {
        return (is_string($precio) || is_int($precio) || is_float($precio))
            && preg_match('/^(?:0|[1-9]\d{0,7})(?:\.\d{1,2})?$/D', (string) $precio) === 1;
    }

    public static function enteroNoNegativo(mixed $valor): bool
    {
        return (is_int($valor) || is_string($valor))
            && preg_match('/^(?:0|[1-9]\d*)$/D', (string) $valor) === 1
            && (float) $valor <= 4294967295;
    }

    public static function precioACentavos(string $precio): int
    {
        if (!self::precioValido($precio)) {
            throw new \InvalidArgumentException('El precio no tiene un formato válido.');
        }
        return Producto::decimalToCents($precio);
    }
}
