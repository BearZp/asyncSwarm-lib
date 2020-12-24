<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

declare(strict_types=1);

namespace Lib\transport;

class UdpSender implements ReloadableInterface, TransportInterface
{
    /**
     * @var string
     */
    private $host;
    /**
     * @var string|null
     */
    private $ip;
    /**
     * @var int
     */
    private $ipLastUpdate = 0;
    /**
     * @var int
     */
    private $ipRefreshPeriod;
    /**
     * @var int
     */
    private $port;
    /**
     * @var resource|null
     */
    private $socket;

    /**
     * UdpSender constructor.
     *
     * @param string $host
     * @param int $port
     * @param int $ipRefreshPeriod
     */
    public function __construct(string $host, int $port, int $ipRefreshPeriod = 1)
    {
        if ($port < 1 || $port > 65535) {
            throw new \InvalidArgumentException("\$port should be in range [1, 65535], {$port} received");
        }
        if ($ipRefreshPeriod < 1) {
            throw new \InvalidArgumentException("Invalid IP refresh period {$ipRefreshPeriod}");
        }
        $this->host = $host;
        $this->port = $port;
        $this->ipRefreshPeriod = $ipRefreshPeriod;
    }

    /**
     * Returns lazy socket
     *
     * @return resource
     * @throws \Exception
     */
    private function getSocket()
    {
        if ($this->socket === null) {
            $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            if ($this->socket === false) {
                $error = socket_last_error();
                throw new \Exception($error . ': ' . socket_strerror($error));
            }
        }
        return $this->socket;
    }


    /**
     * Returns IP, resolved from host
     * @return string
     * @throws \Exception
     */
    public function getIp(): string
    {
        if ($this->ip === null || time() - $this->ipLastUpdate >= $this->ipRefreshPeriod) {
            $this->ip = gethostbyname($this->getHost());
            $this->ipLastUpdate = time();
        }
        if (!filter_var($this->ip, FILTER_VALIDATE_IP)) {
            return '127.0.0.1';
        }
        return $this->ip;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getHost(): string
    {
        if ($this->host === null) {
            throw new \Exception('Invalid UdpSender state - host not provided');
        }
        return $this->host;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getPort(): int
    {
        if ($this->port === null) {
            throw new \Exception('Invalid UdpSender state - port not provided');
        }
        return $this->port;
    }

    /**
     * Sends packet to remote host
     * @param string $packet
     * @throws \Exception
     */
    public function send(string $packet)
    {
        if ($packet === '') {
            // Nothing to send
            return;
        }
        socket_sendto($this->getSocket(), $packet, strlen($packet), 0, $this->getIp(), $this->getPort());
    }

    /**
     * @throws \BadMethodCallException
     */
    public function receive(): string
    {
        throw new \BadMethodCallException('This is a sender class');
    }

    /**
     * Flushes current buffered values
     */
    public function reload()
    {
        if ($this->socket !== null) {
            try {
                socket_close($this->socket);
            } catch (\Exception $ignore) {
            }
        }
        $this->socket = null;
    }
}
