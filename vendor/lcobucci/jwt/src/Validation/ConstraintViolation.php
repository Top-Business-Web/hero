<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Validation;

use Lcobucci\JWT\Exception;
use RuntimeException;

<<<<<<< HEAD
final class ConstraintViolation extends RuntimeException implements Exception
{
=======
use function get_class;

final class ConstraintViolation extends RuntimeException implements Exception
{
    /**
     * @readonly
     * @var class-string<Constraint>|null
     */
    public ?string $constraint = null;

    public static function error(string $message, Constraint $constraint): self
    {
        $exception             = new self($message);
        $exception->constraint = get_class($constraint);

        return $exception;
    }
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
}
