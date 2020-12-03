<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

declare(strict_types=1);

namespace Lib\protocol;

use Lib\queues\rabbitMq\LazyRabbitMqConnectionProvider;
use Lib\queues\rabbitMq\RabbitMqRpcClient;

class AmqpProtocol implements ProtocolInterface
{

    /** @var LazyRabbitMqConnectionProvider */
    private $lazyConnectionProvider;
    /** @var string */
    private $queueName;
    /** @var float */
    private $clientTimeout;
    /** @var RabbitMqRpcClient|null */
    private $rpcClient;
    /** @var bool  */
    private $useRandomResponseQueue;

    /**
     * AmqpProtocol constructor.
     * @param LazyRabbitMqConnectionProvider $provider
     * @param string $queueName
     * @param bool $useRandomResponseQueue
     * @param float $clientTimeout
     */
    public function __construct(
        LazyRabbitMqConnectionProvider $provider,
        string $queueName,
        bool $useRandomResponseQueue,
        float $clientTimeout
    ) {
        $this->lazyConnectionProvider = $provider;
        $this->queueName = $queueName;
        $this->useRandomResponseQueue = $useRandomResponseQueue;
        $this->clientTimeout = $clientTimeout;
    }

    /**
     * @return RabbitMqRpcClient
     * @throws \Exception
     */
    public function getRpcClient(): RabbitMqRpcClient
    {
        if ($this->rpcClient === null) {
            $this->rpcClient = new RabbitMqRpcClient($this->lazyConnectionProvider->getConnection(), false);
        }
        return $this->rpcClient;
    }

    /**
     * @param ProtocolPacketInterface $packet
     * @param string|null $correlationId
     * @throws \Exception
     */
    public function pushPacket(ProtocolPacketInterface $packet, string $correlationId = null): void
    {
        $props = [];
        if ($correlationId !== null) {
            $props['correlation_id'] = $correlationId;
        }
        $this->getRpcClient()->publish($this->queueName, $this->encodePacket($packet), [], $props);
    }

    /**
     * @param ProtocolPacketInterface $packet
     * @return ProtocolPacketInterface
     * @throws \Exception
     */
    public function sendPacket(ProtocolPacketInterface $packet): ProtocolPacketInterface
    {
        $rabbitMqFuture = $this->getRpcClient()->call(
            $this->queueName,
            $this->encodePacket($packet),
            [],
            $this->clientTimeout,
            $this->useRandomResponseQueue
        );
        return new FutureProtocolPacket(function () use ($rabbitMqFuture) {
            $rabbitAnswer = $rabbitMqFuture->get();
            return $this->catchPacket($rabbitAnswer);
        });
    }

    /**
     * @param string $packetBody
     * @return ProtocolPacketInterface
     * @throws \Exception
     */
    public function catchPacket(string $packetBody): ProtocolPacketInterface
    {
        $answer = $this->decodePacket($packetBody);
        if (!isset($answer['data'])) {
            throw new \InvalidArgumentException('$request[\'data\'] is empty');
        }
        if (!\is_array($answer['data'])) {
            throw new \InvalidArgumentException('$request[\'data\'] must be array, got ' . \gettype($answer['data']));
        }
        if (isset($answer['error'])) {
            throw new \Exception($answer['error']);
        }
        return new ProtocolPacket(
            $answer['action'],
            $answer['data'],
            $answer['scope'],
            $answer['requestId']
        );
    }

    /**
     * Utility function to decode any JSON data
     *
     * @param string $str
     * @return array
     * @throws \Exception
     */
    private function decodePacket(string $str): array
    {
        if (empty($str)) {
            throw new \InvalidArgumentException('$str is empty');
        }
        $data  = json_decode(gzuncompress($str), true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            $message = 'JSON decode error: ' . json_last_error();
            if (\function_exists('json_last_error_msg')) {
                $message .= ' ' . json_last_error_msg();
            }
            throw new \Exception($message);
        }
        return $data;
    }



    /**
     * Encodes data before send
     * @param ProtocolPacketInterface $packet
     * @return string
     */
    private function encodePacket(ProtocolPacketInterface $packet): string
    {
        return gzcompress(
            json_encode(
                [
                    'action' => $packet->getAction(),
                    'data' => $packet->getData(),
                    'scope' => $packet->getScope(),
                    'requestId' => $packet->getRequestId()
                ]
            )
        );
    }
}
