<?php
declare(strict_types=1);

namespace App\Models;

final class Producto
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $codigo,
        public readonly string $nombre,
        public readonly ?string $categoria,
        public readonly int $precioVentaCentavos,
        public readonly int $stockActual,
        public readonly int $stockMinimo
    ) {
    }

    /** @param array<string, mixed> $row */
    public static function fromRow(array $row): self
    {
        return new self(
            isset($row['id']) ? (int) $row['id'] : null,
            (string) $row['codigo'],
            (string) $row['nombre'],
            isset($row['categoria']) ? (string) $row['categoria'] : null,
            self::decimalToCents((string) $row['precio_venta']),
            (int) $row['stock_actual'],
            (int) $row['stock_minimo']
        );
    }

    public static function decimalToCents(string $amount): int
    {
        if (!preg_match('/^\d+(?:\.\d{1,2})?$/D', $amount)) {
            throw new \UnexpectedValueException('El importe almacenado no tiene un formato decimal válido.');
        }

        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '');
        $cents = (int) str_pad($fraction, 2, '0');
        return ((int) $whole * 100) + $cents;
    }

    public static function centsToDecimal(int $cents): string
    {
        if ($cents < 0) {
            throw new \InvalidArgumentException('El importe no puede ser negativo.');
        }

        return intdiv($cents, 100) . '.' . str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }
}
