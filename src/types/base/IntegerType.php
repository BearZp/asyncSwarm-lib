<?php
declare(strict_types=1);

/**
 * Created by PhpStorm for WL
 * User: BearZp aka Gulidov Vadim (gulidov.vadim@gmail.com)
 * Date: 14.03.19
 * Time: 11:10
 */

namespace Lib\types\base;

abstract class IntegerType implements TypeInterface
{
    /** @var int */
    protected $value;

    /**
     * Str constructor.
     * @param int $min
     * @param int $max
     * @param int $value
     * @throws \InvalidArgumentException
     */
    public function __construct(int $min, int $max, int $value)
    {
        if ($min > $value || $value > $max) {
            throw new \InvalidArgumentException(
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
     * @return int
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
        return $obj instanceof static and $this->value === $obj->value;
    }
}
