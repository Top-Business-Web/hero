<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Signer\Ecdsa;

use Lcobucci\JWT\Signer\Ecdsa;

use const OPENSSL_ALGO_SHA512;

final class Sha512 extends Ecdsa
{
    public function algorithmId(): string
    {
        return 'ES512';
    }

    public function algorithm(): int
    {
        return OPENSSL_ALGO_SHA512;
    }

<<<<<<< HEAD
    public function keyLength(): int
    {
        return 132;
    }
=======
    public function pointLength(): int
    {
        return 132;
    }

    public function expectedKeyLength(): int
    {
        return 521;
    }
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
}
