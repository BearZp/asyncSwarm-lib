<?php
declare(strict_types=1);

namespace Lib\types;

use Lib\types\base\BinaryType;

class TextType extends BinaryType
{
    public function __construct(string $str)
    {
        parent::__construct($str);
    }
}
