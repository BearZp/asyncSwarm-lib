<?php
/**
* Copyright (C) 2016 - 2020 Wallet Factory International LTD, Wallet Factory PL LTD or their affiliates.
* All rights reserved. <info@walletfactory.com>
* This file is part of Wallet Factory MFS Platform version 1.1.0MFS platform, project #36-16. Files can not
* be used, copied, modified and/or distributed without the express permission of Wallet Factory International LTD,
* Wallet Factory PL LTD or their affiliates. See conditions of permission in the license or source code agreement.
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
