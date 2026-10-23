<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Producto;
use App\Models\Venta;
use DateTimeImmutable;
use PDO;

class VentaRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function insertar(int $productoId, int $cantidad, int $precioUnitarioCentavos, int $totalCentavos): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO venta (producto_id, cantidad, precio_unitario, total)
             VALUES (:producto_id, :cantidad, :precio_unitario, :total)'
        );
        $statement->execute([
            'producto_id' => $productoId,
            'cantidad' => $cantidad,
            'precio_unitario' => Producto::centsToDecimal($precioUnitarioCentavos),
            'total' => Producto::centsToDecimal($totalCentavos),
        ]);
    }

    /** @return list<Venta> */
    public function listarVentasDelDia(DateTimeImmutable $hoy): array
    {
        [$inicio, $fin] = $this->rangoDelDia($hoy);
        $statement = $this->pdo->prepare(
            'SELECT v.id, v.producto_id, p.nombre AS producto_nombre, v.cantidad,
                    v.precio_unitario, v.total, v.fecha_hora
             FROM venta v
             INNER JOIN producto p ON p.id = v.producto_id
             WHERE v.fecha_hora >= :inicio AND v.fecha_hora < :fin
             ORDER BY v.fecha_hora DESC, v.id DESC'
        );
        $statement->execute(['inicio' => $inicio, 'fin' => $fin]);
        $ventas = [];
        foreach ($statement->fetchAll() as $row) {
            $ventas[] = new Venta(
                (int) $row['id'],
                (int) $row['producto_id'],
                (string) $row['producto_nombre'],
                (int) $row['cantidad'],
                Producto::decimalToCents((string) $row['precio_unitario']),
                Producto::decimalToCents((string) $row['total']),
                (string) $row['fecha_hora']
            );
        }
        return $ventas;
    }

    public function totalVendidoDelDia(DateTimeImmutable $hoy): int
    {
        [$inicio, $fin] = $this->rangoDelDia($hoy);
        $statement = $this->pdo->prepare(
            'SELECT COALESCE(SUM(total), 0.00)
             FROM venta
             WHERE fecha_hora >= :inicio AND fecha_hora < :fin'
        );
        $statement->execute(['inicio' => $inicio, 'fin' => $fin]);
        return Producto::decimalToCents((string) $statement->fetchColumn());
    }

    /** @return array{string, string} */
    private function rangoDelDia(DateTimeImmutable $hoy): array
    {
        return [
            $hoy->setTime(0, 0)->format('Y-m-d H:i:s'),
            $hoy->modify('+1 day')->setTime(0, 0)->format('Y-m-d H:i:s'),
        ];
    }
}
