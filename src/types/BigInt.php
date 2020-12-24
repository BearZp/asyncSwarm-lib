<?php
declare(strict_types=1);

namespace Lib\types;

use Lib\types\base\Int;

class BigInt extends Int
{
    public function __construct(int $value)
    {
        parent::__construct(0, 9223372036854775807, $value);
    }
}
