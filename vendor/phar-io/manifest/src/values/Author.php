<?php declare(strict_types = 1);
/*
 * This file is part of PharIo\Manifest.
 *
<<<<<<< HEAD
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Manifest;

use function sprintf;

=======
 * (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PharIo\Manifest;

>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
class Author {
    /** @var string */
    private $name;

<<<<<<< HEAD
    /** @var null|Email */
    private $email;

    public function __construct(string $name, ?Email $email = null) {
=======
    /** @var Email */
    private $email;

    public function __construct(string $name, Email $email) {
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
        $this->name  = $name;
        $this->email = $email;
    }

    public function asString(): string {
<<<<<<< HEAD
        if (!$this->hasEmail()) {
            return $this->name;
        }

        return sprintf(
=======
        return \sprintf(
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
            '%s <%s>',
            $this->name,
            $this->email->asString()
        );
    }

    public function getName(): string {
        return $this->name;
    }

<<<<<<< HEAD
    /**
     * @psalm-assert-if-true Email $this->email
     */
    public function hasEmail(): bool {
        return $this->email !== null;
    }

    public function getEmail(): Email {
        if (!$this->hasEmail()) {
            throw new NoEmailAddressException();
        }

=======
    public function getEmail(): Email {
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
        return $this->email;
    }
}
