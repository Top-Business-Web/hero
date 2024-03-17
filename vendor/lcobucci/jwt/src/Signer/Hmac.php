<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Signer;

use Lcobucci\JWT\Signer;

use function hash_equals;
use function hash_hmac;
<<<<<<< HEAD
=======
use function strlen;
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786

abstract class Hmac implements Signer
{
    final public function sign(string $payload, Key $key): string
    {
<<<<<<< HEAD
=======
        $actualKeyLength   = 8 * strlen($key->contents());
        $expectedKeyLength = $this->minimumBitsLengthForKey();
        if ($actualKeyLength < $expectedKeyLength) {
            throw InvalidKeyProvided::tooShort($expectedKeyLength, $actualKeyLength);
        }

>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
        return hash_hmac($this->algorithm(), $payload, $key->contents(), true);
    }

    final public function verify(string $expected, string $payload, Key $key): bool
    {
        return hash_equals($expected, $this->sign($payload, $key));
    }

    abstract public function algorithm(): string;
<<<<<<< HEAD
=======

    /** @return positive-int */
    abstract public function minimumBitsLengthForKey(): int;
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
}
