<?php
declare(strict_types=1);

namespace Lib\types;

class UuidType
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
}
