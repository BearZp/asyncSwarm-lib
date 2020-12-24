<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

declare(strict_types=1);

namespace Lib\queues\rabbitMq;

use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class LazyRabbitMqConnectionProvider
{
    /** @var AMQPStreamConnection */
    private $connection;
    /** @var string */
    private $host;
    /** @var int */
    private $port;
    /** @var string */
    private $user;
    /** @var string */
    private $pass;
    /** @var string */
    private $vhost;

    /**
     * @param string $host
     * @param int $port
     * @param string $user
     * @param string $pass
     * @param string $path
     */
    public function __construct(
        string $host,
        int $port,
        string $user,
        string $pass,
        string $path
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
        $this->vhost = $path;
    }

    /**
     * @return AbstractConnection
     * @throws \Exception
     */
    public function getConnection(): AbstractConnection
    {
        if ($this->connection === null) {
            try {
                $this->connection = new AMQPStreamConnection(
                    $this->host,
                    $this->port,
                    $this->user,
                    $this->pass,
                    $this->vhost
                );
            } catch (\Exception $error) {
                throw new \Exception(
                    sprintf(
                        'Unable to establish connection to amqp://%s@%s:%s%s using password: %s',
                        $this->user,
                        $this->host,
                        $this->port,
                        $this->vhost,
                        empty($this->pass) ? 'no' : 'yes'
                    ),
                    0,
                    $error
                );
            }
        }
        return $this->connection;
    }


    public function resetConnection(): void
    {
        $this->connection = null;
    }
}
