<?php
declare(strict_types=1);

namespace Lib\types;

use Lib\types\base\TypeInterface;

class BooleanType implements TypeInterface
{
    /** @var boolean */
    private $value;

    /**
     * BoolType constructor.
     * @param bool $value
     */
    public function __construct(bool $value)
    {
        if (!is_bool($value)) {
            throw new \InvalidArgumentException(
                'Bool value Exception. (Value: ' . $value . ')'
            );
        }
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function isTrue(): bool
    {
        return $this->value === true;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->isTrue() ? 'true' : 'false';
    }

    /**
     * @param TypeInterface $obj
     * @return bool
     */
    public function isEqual(TypeInterface $obj): bool
    {
        return $obj instanceof static and $this->value = $obj->value;
    }
}
