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

use const FILTER_VALIDATE_URL;
use function filter_var;

=======
 * (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PharIo\Manifest;

>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
class Url {
    /** @var string */
    private $url;

    public function __construct(string $url) {
        $this->ensureUrlIsValid($url);

        $this->url = $url;
    }

    public function asString(): string {
        return $this->url;
    }

    /**
<<<<<<< HEAD
     * @throws InvalidUrlException
     */
    private function ensureUrlIsValid(string $url): void {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
=======
     * @param string $url
     *
     * @throws InvalidUrlException
     */
    private function ensureUrlIsValid($url): void {
        if (\filter_var($url, \FILTER_VALIDATE_URL) === false) {
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
            throw new InvalidUrlException;
        }
    }
}
