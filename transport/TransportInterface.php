<?php
declare(strict_types=1);
/**
* Copyright (C) 2016 - 2020 Wallet Factory International LTD, Wallet Factory PL LTD or their affiliates.
* All rights reserved. <info@walletfactory.com>
* This file is part of Wallet Factory MFS Platform version 1.1.0MFS platform, project #36-16. Files can not
* be used, copied, modified and/or distributed without the express permission of Wallet Factory International LTD,
* Wallet Factory PL LTD or their affiliates. See conditions of permission in the license or source code agreement.
*/

namespace Lib\transport;

/**
 * Interface TransportInterface
 * @package common\lib\transport
 */
interface TransportInterface
{
    /**
     * Sends packet to remote host
     * @param string $packet
     * @throws \Exception
     */
    public function send(string $packet);

    /**
     * Receive packet from remote host
     * @return string
     * @throws \Exception
     */
    public function receive(): string;
}
