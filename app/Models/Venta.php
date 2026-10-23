<?php
declare(strict_types=1);

namespace App\Models;

final class Venta
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $productoId,
        public readonly string $productoNombre,
        public readonly int $cantidad,
        public readonly int $precioUnitarioCentavos,
        public readonly int $totalCentavos,
        public readonly string $fechaHora
    ) {
    }
}
