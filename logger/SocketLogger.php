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
use Lib\transport\TransportInterface;
use Psr\Log\LoggerInterface;

/**
 * Class UdpLogger
 * @package common\lib\logger
 */
class SocketLogger extends AbstractLogger implements LoggerInterface
{
    /**
     * @var TransportInterface
     */
    private $socket;

    /**
     * @param TransportInterface $socket
     * @param string             $limitLevel
     */
    public function __construct(TransportInterface $socket, string $limitLevel = '')
    {
        parent::__construct($limitLevel);
        $this->socket = $socket;
    }

    /**
     * @param string $packet
     * @throws \Exception
     */
    protected function write(string $packet): void
    {
        $this->socket->send($packet);
    }
}
