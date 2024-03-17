<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Signer;

use Lcobucci\JWT\Signer;

<<<<<<< HEAD
=======
/** @deprecated Deprecated since v4.3 */
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
final class None implements Signer
{
    public function algorithmId(): string
    {
        return 'none';
    }

    // @phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function sign(string $payload, Key $key): string
    {
        return '';
    }

    // @phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function verify(string $expected, string $payload, Key $key): bool
    {
        return $expected === '';
    }
}
