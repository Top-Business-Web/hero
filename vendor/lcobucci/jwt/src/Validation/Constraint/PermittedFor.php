<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Validation\Constraint;

use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\ConstraintViolation;

final class PermittedFor implements Constraint
{
    private string $audience;

    public function __construct(string $audience)
    {
        $this->audience = $audience;
    }

    public function assert(Token $token): void
    {
        if (! $token->isPermittedFor($this->audience)) {
<<<<<<< HEAD
            throw new ConstraintViolation(
                'The token is not allowed to be used by this audience'
=======
            throw ConstraintViolation::error(
                'The token is not allowed to be used by this audience',
                $this
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
            );
        }
    }
}
