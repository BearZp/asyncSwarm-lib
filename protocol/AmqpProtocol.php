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

    /**
     * @param LazyRabbitMqConnectionProvider $provider
     * @param string $queueName
     * @param float $clientTimeout
     */
    public function __construct(
        LazyRabbitMqConnectionProvider $provider,
        string $queueName,
        float $clientTimeout
    ) {
        $this->lazyConnectionProvider = $provider;
        $this->queueName = $queueName;
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
     * @throws \Exception
     */
    public function pushPacket(ProtocolPacketInterface $packet): void
    {
        $this->getRpcClient()->publish($this->queueName, $this->encodePacket($packet));
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
            $this->clientTimeout
        );
        return new FutureProtocolPacket(function () use ($rabbitMqFuture) {
            $rabbitAnswer = $rabbitMqFuture->get();
            return $this->createAnswer($rabbitAnswer);
        });
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
     * @param string $answerBody
     * @return ProtocolPacketInterface
     * @throws \Exception
     */
    public function createAnswer(string $answerBody): ProtocolPacketInterface
    {
        $answer = $this->decodePacket($answerBody);
        if (!isset($answer['payload'])) {
            throw new \InvalidArgumentException('$request[\'payload\'] is empty');
        }
        if (!\is_array($answer['payload'])) {
            throw new \InvalidArgumentException('$request[\'payload\'] must be array, got ' . \gettype($answer['payload']));
        }
        if (isset($answer['error'])) {
            throw new \Exception($answer['error']);
        }
        return new ProtocolPacket(
            'Answer',
            $answer['data'],
            $answer['scope'],
            $answer['requestId']
        );
    }

    /**
     * Encodes data before send
     * @param ProtocolPacketInterface $packet
     * @return string
     */
    public function encodePacket(ProtocolPacketInterface $packet): string
    {
        return gzcompress(json_encode([
            'action' => $packet->getAction(),
            'data' => $packet->getData(),
            'scope' => $packet->getScope(),
            'requestId' => $packet->getRequestId()
        ]));
    }
}
