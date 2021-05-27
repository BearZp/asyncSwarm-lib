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

    /** @var object */
    private $data;

    /** @var object */
    private $scope;

    /** @var string */
    private $requestId;

    /**
     * ProtocolPacket constructor.
     * @param string $action
     * @param object $data
     * @param object $scope
     * @param string $requestId
     */
    public function __construct(
        string $action,
        object $data,
        object $scope,
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
     * @return object
     */
    public function getData(): object
    {
        return $this->data;
    }

    /**
     * @return object
     */
    public function getScope():object
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

    public function toString(): string
    {
        return json_encode([
            'action' => $this->action,
            'data' => $this->data,
            'scope' => $this->scope,
            'request_id' => $this->requestId,
        ]);
    }
}
