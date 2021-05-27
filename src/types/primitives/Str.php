<?php
declare(strict_types=1);

namespace Lib\types\primitives;

use InvalidArgumentException;
use function mb_strlen;

class Str
{
    /**
     * @var string
     */
    protected $str;

    /**
     * Str constructor.
     * @param int $minLen
     * @param int $maxLen
     * @param string $str
     * @throws InvalidArgumentException
     */
    public function __construct(int $minLen, int $maxLen, string $str)
    {
        $strLen = mb_strlen($str);
        if ($minLen > $strLen || $strLen > $maxLen) {
            throw new InvalidArgumentException(
                'String length Exception. (Length: ' . $strLen . ') Min: ' . $minLen . ' Max: ' . $maxLen
            );
        }
        $this->str = $str;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->str;
    }

    /**
     * @param Str $obj
     * @return bool
     */
    public function isEqual(Str $obj): bool
    {
        return $this->__toString() === $obj->__toString();
    }
}
