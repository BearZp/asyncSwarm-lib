<?php
declare(strict_types=1);

namespace Lib\types\base;

interface TypeInterface
{
    /**
     * @return string
     */
    public function toString(): string;

    /**
     * @param TypeInterface $obj
     * @return bool
     */
    public function isEqual(TypeInterface $obj): bool;
}
