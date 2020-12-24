<?php
declare(strict_types=1);

namespace Lib\types;

use Lib\types\base\Str;

class Str255 extends Str
{
    public function __construct(string $str)
    {
        parent::__construct(0, 255, $str);
    }
}
