<?php
declare(strict_types=1);

namespace Lib\types;

use Lib\types\base\Int as BaseIntType;

class Int extends BaseIntType
{
    public function __construct(int $value)
    {
        parent::__construct(0, 2147483647, $value);
    }
}
