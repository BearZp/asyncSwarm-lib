<?php
declare(strict_types=1);

namespace Lib\types;

class Text extends Binary
{
    public function __construct(string $str)
    {
        parent::__construct($str);
    }
}
