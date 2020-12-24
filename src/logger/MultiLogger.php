<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

declare(strict_types=1);

namespace Lib\logger;

use Psr\Log\LoggerInterface;
use Psr\Log\AbstractLogger;

class MultiLogger extends AbstractLogger implements LoggerInterface
{
    /** @var array */
    private $collection;

    /**
     * @param LoggerInterface $logger
     */
    public function addLogger(LoggerInterface $logger): void
    {
        $this->collection[spl_object_id($logger)] = $logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function removeLogger(LoggerInterface $logger): void
    {
        if(isset($this->collection[spl_object_id($logger)])) {
            unset($this->collection[spl_object_id($logger)]);
        }
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @throws \Exception
     */
    public function log($level, $message, array $context = array())
    {
        if (!$this->collection) {
            throw new \Exception('No logger founded');
        }

        $exceptions = [];
        /** @var LoggerInterface $logger */
        foreach ($this->collection as $logger) {
            try {
                $logger->log($level, $message, $context);
            } catch (\Throwable $e) {
                $exceptions[] = $e;
            }
        }

        if($exceptions) {
            throw current($exceptions);
        }
    }
}