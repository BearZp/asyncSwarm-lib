<?php
declare(strict_types=1);

namespace Lib\types;

use Lib\types\base\IntType as BaseIntType;

class IntType extends BaseIntType
{
    public function __construct(int $value)
    {
        parent::__construct(0, 2147483647, $value);
    }
}
