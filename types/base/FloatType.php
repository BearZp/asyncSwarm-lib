<?php
declare(strict_types=1);

/**
 * Created by PhpStorm for WL
 * User: BearZp aka Gulidov Vadim (gulidov.vadim@gmail.com)
 * Date: 14.03.19
 * Time: 11:10
 */

namespace Lib\types\base;

abstract class FloatType implements TypeInterface
{
    /** @var float */
    protected $value;

    /** @var int */
    protected $precision;

    /**
     * @param int   $min
     * @param int   $max
     * @param int   $precision
     * @param float $value
     */
    public function __construct(int $min, int $max, int $precision, float $value)
    {
        if ($min > $value || $value > $max) {
            throw new \InvalidArgumentException(
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
        return (int) $this->toFloat();
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return (string) $this->value;
    }

    /**
     * @param TypeInterface $obj
     * @return bool
     */
    public function isEqual(TypeInterface $obj): bool
    {
        return $obj instanceof static and $this->precision = $obj->precision and $this->value = $obj->value;
    }
}
