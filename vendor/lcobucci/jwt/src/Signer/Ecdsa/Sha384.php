<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Signer\Ecdsa;

use Lcobucci\JWT\Signer\Ecdsa;

use const OPENSSL_ALGO_SHA384;

final class Sha384 extends Ecdsa
{
    public function algorithmId(): string
    {
        return 'ES384';
    }

    public function algorithm(): int
    {
        return OPENSSL_ALGO_SHA384;
    }

<<<<<<< HEAD
    public function keyLength(): int
    {
        return 96;
    }
=======
    public function pointLength(): int
    {
        return 96;
    }

    public function expectedKeyLength(): int
    {
        return 384;
    }
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
}
