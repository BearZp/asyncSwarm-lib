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
     * @return object
     */
    public function getData(): object;

    /**
     * @return object
     */
    public function getScope():object;

    /**
     * @return string
     */
    public function getRequestId(): string;
}
