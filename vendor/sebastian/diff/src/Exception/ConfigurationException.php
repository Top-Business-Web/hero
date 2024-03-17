<?php declare(strict_types=1);
/*
 * This file is part of sebastian/diff.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Diff;

use function get_class;
use function gettype;
use function is_object;
use function sprintf;
use Exception;

final class ConfigurationException extends InvalidArgumentException
{
    public function __construct(
        string $option,
        string $expected,
        $value,
        int $code = 0,
<<<<<<< HEAD
        ?Exception $previous = null
=======
        Exception $previous = null
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
    ) {
        parent::__construct(
            sprintf(
                'Option "%s" must be %s, got "%s".',
                $option,
                $expected,
                is_object($value) ? get_class($value) : (null === $value ? '<null>' : gettype($value) . '#' . $value)
            ),
            $code,
            $previous
        );
    }
}
