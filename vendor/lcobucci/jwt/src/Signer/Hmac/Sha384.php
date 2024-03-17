<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Signer\Hmac;

use Lcobucci\JWT\Signer\Hmac;

final class Sha384 extends Hmac
{
    public function algorithmId(): string
    {
        return 'HS384';
    }

    public function algorithm(): string
    {
        return 'sha384';
    }
<<<<<<< HEAD
=======

    public function minimumBitsLengthForKey(): int
    {
        return 384;
    }
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
}
