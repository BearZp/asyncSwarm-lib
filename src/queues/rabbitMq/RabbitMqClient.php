<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

declare(strict_types=1);

namespace Lib\queues\rabbitMq;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqClient
{
    /**
     * @var AbstractConnection
     */
    protected $connection;
    /**
     * @var AMQPChannel
     */
    protected $channel;
    /**
     * @var bool
     */
    protected $durable;
    /**
     * @var array List of declared queues
     */
    private $declaredQueues = [];

    /**
     * Constructor
     *
     * @param AbstractConnection $connection
     * @param bool $durable
     */
    public function __construct(AbstractConnection $connection, bool $durable = false)
    {
        $this->connection = $connection;
        $this->durable = $durable;
    }

    /**
     * @param string $name
     */
    protected function prepareQueue(string $name)
    {
        if ($this->channel === null) {
            $this->channel = $this->connection->channel();
        }
        if (!isset($this->declaredQueues[$name])) {
            $this->channel->queue_declare($name, false, $this->durable, false, false);
            $this->declaredQueues[$name] = true;
        }
    }

    /**
     * Publishes information and DOES NOT wait for response
     * This is common fire-and-forget, use {@see call} for response
     * @param string $name
     * @param string $body
     * @param array $meta
     * @param array $props
     * @return void
     */
    public function publish(string $name, string $body, array $meta = [], array $props = [])
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('$name must be a not empty string');
        }
        if (empty($body)) {
            throw new \InvalidArgumentException('$body must be a not empty string');
        }
        $this->prepareQueue($name);
        if (count($meta) > 0) {
            $props['application_headers'] = [];
            foreach ($meta as $key => $value) {
                $props['application_headers'][$key] = ['S', $value];
            }
        }

        $this->channel->basic_publish(new AMQPMessage($body, $props), '', $name);
    }

    /**
     * Get single message from queue
     * @param string $name
     * @param bool $noAkc
     * @return AMQPMessage|null
     */
    public function getMessage(string $name, bool $noAkc = false)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('$name must be a not empty string');
        }
        $this->prepareQueue($name);
        return $this->channel->basic_get($name, $noAkc);
    }

    /**
     * Ack selected message
     * @param AMQPMessage $message
     */
    public function ackMessage(AMQPMessage $message)
    {
        /** @var string $routingKey */
        $routingKey = $message->delivery_info['routing_key'];
        $this->prepareQueue($routingKey);
        $this->channel->basic_ack($message->delivery_info['delivery_tag']);
    }
}
