<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Signer\Ecdsa;

use Lcobucci\JWT\Signer\Ecdsa;

use const OPENSSL_ALGO_SHA256;

final class Sha256 extends Ecdsa
{
    public function algorithmId(): string
    {
        return 'ES256';
    }

    public function algorithm(): int
    {
        return OPENSSL_ALGO_SHA256;
    }

<<<<<<< HEAD
    public function keyLength(): int
    {
        return 64;
    }
=======
    public function pointLength(): int
    {
        return 64;
    }

    public function expectedKeyLength(): int
    {
        return 256;
    }
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
}
