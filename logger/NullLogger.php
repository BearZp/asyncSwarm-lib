<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 03.11.20
 * Time: 17:17
 */

namespace Lib\logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

class NullLogger extends AbstractLogger implements LoggerInterface
{
    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        return;
    }
}