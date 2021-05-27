<?php
declare(strict_types=1);

namespace Lib\types\primitives;

use InvalidArgumentException;

class IntType
{
    /**
     * @var int
     */
    protected $value;

    /**
     * Str constructor.
     * @param int $min
     * @param int $max
     * @param int $value
     * @throws InvalidArgumentException
     */
    public function __construct(int $min, int $max, int $value)
    {
        if ($min > $value || $value > $max) {
            throw new InvalidArgumentException(
                'Int value Exception. (Value: ' . $value . ') Min: ' . $min . ' Max: ' . $max
            );
        }
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function toInteger(): int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->value;
    }
}
