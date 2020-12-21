<?php declare(strict_types=1);

namespace Lib\protocol;

use Lib\transport\HttpConnectionProvider;
use Lib\transport\HttpSwooleRpcClient;
use Lib\transport\RpcClientInterface;

class HttpProtocol implements ProtocolInterface
{
    /**@var int */
    private $clientConnectionTimeout;

    /** @var RpcClientInterface */
    private $client;

    /**
     * @var RpcClientInterface
     */
    private $rpcClient;

    private $lazyConnectionProvider;

    /**
     * HttpProtocol constructor.
     * @param HttpConnectionProvider $lazyConnectionProvider
     * @param int $clientConnectionTimeout
     */
    public function __construct(
        HttpConnectionProvider $lazyConnectionProvider,
        int $clientConnectionTimeout
    ) {
        $this->lazyConnectionProvider = $lazyConnectionProvider;
        $this->clientConnectionTimeout = $clientConnectionTimeout;
    }

    /**
     * @return RpcClientInterface
     * @throws \Exception
     */
    public function getRpcClient(): RpcClientInterface
    {
        if ($this->rpcClient === null) {
            $this->rpcClient = new HttpSwooleRpcClient($this->lazyConnectionProvider->getConnection(), false);
        }
        return $this->rpcClient;
    }

    /**
     * @param ProtocolPacketInterface $packet
     * @throws \Exception
     */
    public function pushPacket(ProtocolPacketInterface $packet): void
    {
        $this->getRpcClient()->call(
            $packet->getAction(),
            $this->encodePacket($packet),
            [],
            $this->clientConnectionTimeout
        );
    }

    /**
     * @param ProtocolPacketInterface $packet
     * @return ProtocolPacketInterface
     * @throws \Exception
     */
    public function sendPacket(ProtocolPacketInterface $packet): ProtocolPacketInterface
    {
        $response = $this->getRpcClient()->call(
            $packet->getAction(),
            $this->encodePacket($packet),
            [],
            $this->clientConnectionTimeout
        );

        return new FutureProtocolPacket(function() use ($response) {
            return $this->catchPacket($response->get());
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
        if (!isset($answer['result'])) {
            throw new \InvalidArgumentException('$request[\'result\'] is empty');
        }
        if (!\is_array($answer['result'])) {
            throw new \InvalidArgumentException('$request[\'result\'] must be array, got ' . \gettype($answer['data']));
        }
        if (isset($answer['error'])) {
            throw new \Exception($answer['error']);
        }
        return new ProtocolPacket(
            $answer['method'],
            $answer['result'],
            $answer['scope'],
            $answer['id']
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
        $data  = json_decode($str, true);
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
     * @param ProtocolPacketInterface $packet
     * @return string
     */
    private function encodeResponse(ProtocolPacketInterface $packet): string
    {
        return json_encode([
            'jsonrpc' => '2.0',
            'method' => $packet->getAction(),
            'result' => $packet->getData(),
            'scope' => $packet->getScope(),
            'id' => $packet->getRequestId()
        ]);
    }

    /**
     * Encodes data before send
     * @param ProtocolPacketInterface $packet
     * @return string
     */
    private function encodePacket(ProtocolPacketInterface $packet): string
    {
        return json_encode([
            'jsonrpc' => '2.0',
            'method' => $packet->getAction(),
            'params' => $packet->getData(),
            'scope' => $packet->getScope(),
            'id' => $packet->getRequestId()
        ]);
    }
}