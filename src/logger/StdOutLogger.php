<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

declare(strict_types=1);

namespace common\lib\logger;

use Lib\logger\AbstractLogger;

class StdOutLogger extends AbstractLogger
{
    /**
     * @param string $packet
     */
    protected function write(string $packet): void
    {
        echo $packet;
    }
}
