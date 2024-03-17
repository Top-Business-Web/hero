<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Validation\Constraint;

use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Token;
<<<<<<< HEAD
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\ConstraintViolation;

final class SignedWith implements Constraint
=======
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\ConstraintViolation;
use Lcobucci\JWT\Validation\SignedWith as SignedWithInterface;

final class SignedWith implements SignedWithInterface
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
{
    private Signer $signer;
    private Signer\Key $key;

    public function __construct(Signer $signer, Signer\Key $key)
    {
        $this->signer = $signer;
        $this->key    = $key;
    }

    public function assert(Token $token): void
    {
<<<<<<< HEAD
        if (! $token instanceof Token\Plain) {
            throw new ConstraintViolation('You should pass a plain token');
        }

        if ($token->headers()->get('alg') !== $this->signer->algorithmId()) {
            throw new ConstraintViolation('Token signer mismatch');
        }

        if (! $this->signer->verify($token->signature()->hash(), $token->payload(), $this->key)) {
            throw new ConstraintViolation('Token signature mismatch');
=======
        if (! $token instanceof UnencryptedToken) {
            throw ConstraintViolation::error('You should pass a plain token', $this);
        }

        if ($token->headers()->get('alg') !== $this->signer->algorithmId()) {
            throw ConstraintViolation::error('Token signer mismatch', $this);
        }

        if (! $this->signer->verify($token->signature()->hash(), $token->payload(), $this->key)) {
            throw ConstraintViolation::error('Token signature mismatch', $this);
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
        }
    }
}
