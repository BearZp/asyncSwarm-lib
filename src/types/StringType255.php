<?php
declare(strict_types=1);

namespace Lib\types;

use Lib\types\base\StringType;

class StringType255 extends StringType
{
    public function __construct(string $str)
    {
        parent::__construct(0, 255, $str);
    }
}
