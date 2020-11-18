<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

declare(strict_types=1);

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
