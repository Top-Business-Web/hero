<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Signer\Key;

<<<<<<< HEAD
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Signer\Key;
=======
use Lcobucci\JWT\Signer\InvalidKeyProvided;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\SodiumBase64Polyfill;
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
use SplFileObject;
use Throwable;

use function assert;
<<<<<<< HEAD
use function base64_decode;
=======
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
use function is_string;

final class InMemory implements Key
{
    private string $contents;
    private string $passphrase;

<<<<<<< HEAD
    private function __construct(string $contents, string $passphrase)
    {
=======
    /** @param non-empty-string $contents */
    private function __construct(string $contents, string $passphrase)
    {
        // @phpstan-ignore-next-line
        if ($contents === '') {
            throw InvalidKeyProvided::cannotBeEmpty();
        }

>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
        $this->contents   = $contents;
        $this->passphrase = $passphrase;
    }

<<<<<<< HEAD
    public static function empty(): self
    {
        return new self('', '');
    }

=======
    /** @deprecated Deprecated since v4.3 */
    public static function empty(): self
    {
        $emptyKey             = new self('empty', 'empty');
        $emptyKey->contents   = '';
        $emptyKey->passphrase = '';

        return $emptyKey;
    }

    /** @param non-empty-string $contents */
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
    public static function plainText(string $contents, string $passphrase = ''): self
    {
        return new self($contents, $passphrase);
    }

<<<<<<< HEAD
    public static function base64Encoded(string $contents, string $passphrase = ''): self
    {
        $decoded = base64_decode($contents, true);

        if ($decoded === false) {
            throw CannotDecodeContent::invalidBase64String();
        }

=======
    /** @param non-empty-string $contents */
    public static function base64Encoded(string $contents, string $passphrase = ''): self
    {
        $decoded = SodiumBase64Polyfill::base642bin(
            $contents,
            SodiumBase64Polyfill::SODIUM_BASE64_VARIANT_ORIGINAL
        );

        // @phpstan-ignore-next-line
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
        return new self($decoded, $passphrase);
    }

    /** @throws FileCouldNotBeRead */
    public static function file(string $path, string $passphrase = ''): self
    {
        try {
            $file = new SplFileObject($path);
        } catch (Throwable $exception) {
            throw FileCouldNotBeRead::onPath($path, $exception);
        }

        $contents = $file->fread($file->getSize());
        assert(is_string($contents));
<<<<<<< HEAD
=======
        assert($contents !== '');
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786

        return new self($contents, $passphrase);
    }

    public function contents(): string
    {
        return $this->contents;
    }

    public function passphrase(): string
    {
        return $this->passphrase;
    }
}
