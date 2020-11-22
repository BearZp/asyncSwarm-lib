<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

declare(strict_types=1);

namespace Lib\protocol;

use Lib\models\FutureObjectTrait;

class FutureProtocolPacket implements ProtocolPacketInterface
{
    use FutureObjectTrait;

    /**
     * @return string
     * @throws \Throwable
     */
    public function getAction(): string
    {
        return $this->get()->getAction();
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function getPayload(): array
    {
        return $this->get()->getPayload();
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function getScope(): array
    {
        return $this->get()->getScope();
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function getResponseChanel(): string
    {
        return $this->get()->getResponseChanel();
    }
}
