<?php
declare(strict_types=1);

namespace Lib\types;

use Lib\types\base\StrType;

class Str255Type extends StrType
{
    public function __construct(string $str)
    {
        parent::__construct(0, 255, $str);
    }
}
