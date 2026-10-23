<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Producto;
use PDO;

class ProductoRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function insertar(Producto $producto): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO producto (codigo, nombre, categoria, precio_venta, stock_actual, stock_minimo)
             VALUES (:codigo, :nombre, :categoria, :precio_venta, :stock_actual, :stock_minimo)'
        );
        $statement->execute([
            'codigo' => $producto->codigo,
            'nombre' => $producto->nombre,
            'categoria' => $producto->categoria,
            'precio_venta' => Producto::centsToDecimal($producto->precioVentaCentavos),
            'stock_actual' => $producto->stockActual,
            'stock_minimo' => $producto->stockMinimo,
        ]);
    }

    public function codigoExiste(string $codigo): bool
    {
        $statement = $this->pdo->prepare('SELECT 1 FROM producto WHERE codigo = :codigo LIMIT 1');
        $statement->execute(['codigo' => $codigo]);
        return $statement->fetchColumn() !== false;
    }

    public function obtenerPorId(int $id, bool $bloquear = false): ?Producto
    {
        $sql = 'SELECT id, codigo, nombre, categoria, precio_venta, stock_actual, stock_minimo
                FROM producto WHERE id = :id';
        if ($bloquear) {
            $sql .= ' FOR UPDATE';
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : Producto::fromRow($row);
    }

    /** @return list<Producto> */
    public function listarTodos(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, codigo, nombre, categoria, precio_venta, stock_actual, stock_minimo
             FROM producto ORDER BY nombre, codigo'
        );
        $productos = [];
        foreach ($statement->fetchAll() as $row) {
            $productos[] = Producto::fromRow($row);
        }
        return $productos;
    }

    public function descontarStock(int $id, int $cantidad): int
    {
        $statement = $this->pdo->prepare(
            'UPDATE producto
             SET stock_actual = stock_actual - :cantidad
             WHERE id = :id AND stock_actual >= :cantidad_disponible'
        );
        $statement->execute([
            'cantidad' => $cantidad,
            'id' => $id,
            'cantidad_disponible' => $cantidad,
        ]);
        return $statement->rowCount();
    }
}
