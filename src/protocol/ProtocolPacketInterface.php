<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

declare(strict_types=1);

namespace Lib\protocol;

interface ProtocolPacketInterface
{
    /**
     * @return string
     */
    public function getAction(): string;

    /**
     * @return array
     */
    public function getData(): array;

    /**
     * @return array
     */
    public function getScope():array;

    /**
     * @return string
     */
    public function getRequestId(): string;
}
