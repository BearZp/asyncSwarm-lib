<?php
declare(strict_types=1);

namespace Lib\types;

class Uuid
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @param string $uuid
     * @throws \InvalidArgumentException
     */
    public function __construct(string $uuid)
    {
        $uuidLen = \strlen($uuid);
        if ($uuidLen === 16) {
            $uuid = \bin2hex($uuid);
        } elseif ($uuidLen === 0) {
            $uuid = '00000000000000000000000000000000';
        } elseif ($uuidLen !== 32) {
            throw new \InvalidArgumentException('Invalid uuid');
        }
        $this->uuid = \mb_strtoupper($uuid);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function toBin(): string
    {
        return \hex2bin($this->uuid);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->uuid === '00000000000000000000000000000000' || !$this->uuid;
    }

    /**
     * @param UuidType $uuid
     * @return bool
     */
    public function isEqual($uuid): bool
    {
        return $this->uuid === $uuid->toString();
    }

    /**
     * @param int $length
     * @return self
     * @throws \Exception
     */
    public function generateRandom(int $length = 32): self
    {
        if (function_exists("random_bytes")) {
            $bytes = random_bytes((int) ceil($length / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes((int) ceil($length / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return new Uuid($bytes);
    }
}
