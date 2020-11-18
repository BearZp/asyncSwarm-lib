<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

declare(strict_types=1);

namespace Lib\protocol;

class ProtocolPacket implements ProtocolPacketInterface
{
    /** @var string */
    private $action;

    /** @var array */
    private $payload;

    /** @var array */
    private $scope;

    /** @var string */
    private $responseChanel;

    /**
     * ProtocolPacket constructor.
     * @param string $action
     * @param array $payload
     * @param array $scope
     * @param string|null $responseChanel
     */
    public function __construct(string $action, array $payload, array $scope, string $responseChanel = null)
    {
        $this->action = $action;
        $this->payload = $payload;
        $this->scope = $scope;
        $this->responseChanel = $responseChanel;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @return array
     */
    public function getScope():array
    {
        return $this->scope;
    }

    /**
     * @return string
     */
    public function getResponseChanel(): string
    {
        return $this->responseChanel;
    }
}
