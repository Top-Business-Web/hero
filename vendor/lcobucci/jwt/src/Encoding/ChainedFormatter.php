<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Encoding;

use Lcobucci\JWT\ClaimsFormatter;

final class ChainedFormatter implements ClaimsFormatter
{
    /** @var list<ClaimsFormatter> */
    private array $formatters;

    public function __construct(ClaimsFormatter ...$formatters)
    {
        $this->formatters = $formatters;
    }

    public static function default(): self
    {
        return new self(new UnifyAudience(), new MicrosecondBasedDateConversion());
    }

<<<<<<< HEAD
=======
    public static function withUnixTimestampDates(): self
    {
        return new self(new UnifyAudience(), new UnixTimestampDates());
    }

>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
    /** @inheritdoc */
    public function formatClaims(array $claims): array
    {
        foreach ($this->formatters as $formatter) {
            $claims = $formatter->formatClaims($claims);
        }

        return $claims;
    }
}
