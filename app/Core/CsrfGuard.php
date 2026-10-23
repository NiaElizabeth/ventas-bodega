<?php
declare(strict_types=1);

namespace App\Core;

use App\Exceptions\TokenCsrfInvalidoException;

final class CsrfGuard
{
    public function token(): string
    {
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return (string) $_SESSION['_csrf_token'];
    }

    public function verificar(mixed $token): void
    {
        if (!is_string($token) || !hash_equals($this->token(), $token)) {
            throw new TokenCsrfInvalidoException('Token CSRF ausente o inválido.');
        }
    }
}
