<?php
declare(strict_types=1);

namespace Lib\types\primitives;

use InvalidArgumentException;

class FloatType
{
    /**
     * @var float
     */
    protected $value;

    /**
     * @var int
     */
    protected $precision;

    /**
     * @param int   $min
     * @param int   $max
     * @param int   $precision
     * @param float $value
     * @throws InvalidArgumentException
     */
    public function __construct(int $min, int $max, int $precision, float $value)
    {
        if ($min > $value || $value > $max) {
            throw new InvalidArgumentException(
                'Int value Exception. (Value: ' . $value . ') Min: ' . $min . ' Max: ' . $max
            );
        }

        $this->value = $value;
        $this->precision = $precision;
    }

    /**
     * @return float
     */
    public function toFloat(): float
    {
        return round($this->value, $this->precision);
    }

    /**
     * @return int
     */
    public function toInteger(): int
    {
        return (int)round($this->value);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->value;
    }
}
