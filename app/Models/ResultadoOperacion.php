<?php
declare(strict_types=1);

namespace App\Models;

final class ResultadoOperacion
{
    /** @param list<string> $errores */
    private function __construct(
        public readonly bool $exitoso,
        public readonly array $errores = []
    ) {
    }

    public static function exito(): self
    {
        return new self(true);
    }

    /** @param list<string> $errores */
    public static function fallo(array $errores): self
    {
        return new self(false, $errores);
    }
}
