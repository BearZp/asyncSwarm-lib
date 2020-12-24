<?php declare(strict_types = 1);

/**
 * Created by PhpStorm for WL
 * User: BearZp aka Gulidov Vadim (gulidov.vadim@gmail.com)
 * Date: 14.03.19
 * Time: 11:10
 */

namespace Lib\types;

use Lib\types\base\TypeInterface;

class Binary implements TypeInterface
{
    /** @var string */
    protected $str;

    /**
     * Str constructor.
     * @param string $str
     * @throws \InvalidArgumentException
     */
    public function __construct(string $str)
    {
        $this->str = $str;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->str;
    }

    /**
     * @param TypeInterface $obj
     * @return bool
     */
    public function isEqual(TypeInterface $obj): bool
    {
        return $obj instanceof static and $this->str === $obj->str;
    }
}
