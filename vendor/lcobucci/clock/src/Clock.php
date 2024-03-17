<?php
declare(strict_types=1);

namespace Lcobucci\Clock;

use DateTimeImmutable;
<<<<<<< HEAD
use StellaMaris\Clock\ClockInterface;
=======
use Psr\Clock\ClockInterface;
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786

interface Clock extends ClockInterface
{
    public function now(): DateTimeImmutable;
}
