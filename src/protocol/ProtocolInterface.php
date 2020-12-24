<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

declare(strict_types=1);

namespace Lib\protocol;

interface ProtocolInterface
{
    /**
     * @param ProtocolPacketInterface $packet
     * @return ProtocolPacketInterface
     */
    public function sendPacket(ProtocolPacketInterface $packet): ProtocolPacketInterface;

    /**
     * @param ProtocolPacketInterface $packet
     * @return void
     */
    public function pushPacket(ProtocolPacketInterface $packet): void;
}
