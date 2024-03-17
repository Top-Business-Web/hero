<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Token;

use function array_key_exists;

final class DataSet
{
    /** @var array<string, mixed> */
    private array $data;
    private string $encoded;

<<<<<<< HEAD
    /** @param mixed[] $data */
=======
    /** @param array<string, mixed> $data */
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
    public function __construct(array $data, string $encoded)
    {
        $this->data    = $data;
        $this->encoded = $encoded;
    }

    /**
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function get(string $name, $default = null)
    {
        return $this->data[$name] ?? $default;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->data);
    }

<<<<<<< HEAD
    /** @return mixed[] */
=======
    /** @return array<string, mixed> */
>>>>>>> 152c5ac8b3fa0942a784ef128282fb9c55e17786
    public function all(): array
    {
        return $this->data;
    }

    public function toString(): string
    {
        return $this->encoded;
    }
}
