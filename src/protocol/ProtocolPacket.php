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
    private $data;

    /** @var array */
    private $scope;

    /** @var string */
    private $requestId;

    /**
     * ProtocolPacket constructor.
     * @param string $action
     * @param array $data
     * @param array $scope
     * @param string $requestId
     */
    public function __construct(
        string $action,
        array $data,
        array $scope,
        string $requestId
    ) {
        $this->action = $action;
        $this->data = $data;
        $this->scope = $scope;
        $this->requestId = $requestId;
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
    public function getData(): array
    {
        return $this->data;
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
    public function getRequestId(): string
    {
        return $this->requestId;
    }
}
