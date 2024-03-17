<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Signer;

use InvalidArgumentException;
use Lcobucci\JWT\Exception;

final class CannotSignPayload extends InvalidArgumentException implements Exception
{
    public static function errorHappened(string $error): self
    {
<<<<<<< HEAD
        return new self('There was an error while creating the signature: ' . $error);
=======
        return new self('There was an error while creating the signature:' . $error);
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
    }
}
