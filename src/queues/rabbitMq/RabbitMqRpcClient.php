<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

declare(strict_types=1);

namespace Lib\queues\rabbitMq;

use Lib\transport\FutureAnswerBundleInterface;
use Lib\transport\RpcClientInterface;
use Lib\types\Uuid;
use PhpAmqpLib\Exception\AMQPIOWaitException;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqRpcClient extends RabbitMqClient implements RpcClientInterface
{
    /** @var string */
    private $anonymousCallbackQueue;
    /** @var RabbitMqFuture[] */
    private $cMap = [];

    /**
     * Performs RPC call to remote worker
     *
     * @param string $name
     * @param string $body
     * @param array $meta
     * @param float|null $timeout
     * @param bool $useRandomResponseQueue
     * @return RabbitMqFuture
     * @throws \Exception
     */
    public function call(
        string $name,
        string $body,
        array $meta = [],
        float $timeout = null,
        bool $useRandomResponseQueue = false
    ): FutureAnswerBundleInterface {
        if (empty($body)) {
            throw new \InvalidArgumentException('$body must be a not empty string');
        }
        $this->prepareQueue($name);
        $correlationId = uniqid('call', true) . random_int(10000, 99999);
        $anonymousName = $this->makeAnonymousQueue($useRandomResponseQueue);
        $props = [
            'correlation_id' => $correlationId,
            'reply_to' => $anonymousName
        ];
        if (count($meta) > 0) {
            $props['application_headers'] = [];
            foreach ($meta as $key => $value) {
                $props['application_headers'][$key] = ['S', $value];
            }
        }
        $message = new AMQPMessage(
            $body,
            $props
        );
        $response = new RabbitMqFuture($this, $name, $timeout);
        $this->cMap[$correlationId] = $response;
        $this->channel->basic_publish($message, "", $name);
        return $response;
    }

    /**
     * Utility method to create (if not created) and return name of temporary queue
     *
     * @param bool $useRandomResponseQueue
     * @return string
     * @throws \Exception
     */
    private function makeAnonymousQueue(bool $useRandomResponseQueue = false): string
    {
        if ($this->channel === null) {
            $this->channel = $this->connection->channel();
        }
        if ($this->anonymousCallbackQueue === null) {
            $queueName = '';
            if ($useRandomResponseQueue) {
                $queueName = (new Uuid(''))->generateRandom()->toString();
            }

            $res = $this->channel->queue_declare($queueName, false, false, false, false);
            $this->anonymousCallbackQueue = $res[0];
            $this->channel->basic_consume(
                $this->anonymousCallbackQueue,
                "",
                false,
                true,
                false,
                false,
                [$this, 'onMessage']
            );
        }
        return $this->anonymousCallbackQueue;
    }

    /**
     * @param AMQPMessage $rep
     */
    public function onMessage(AMQPMessage $rep)
    {
        if (isset($this->cMap[$rep->get('correlation_id')])) {
            $this->cMap[$rep->get('correlation_id')]->complete($rep->body);
            unset($this->cMap[$rep->get('correlation_id')]);
        }

        if ($this->anonymousCallbackQueue) {
            $this->connection->channel()->queue_delete($this->anonymousCallbackQueue);
            $this->anonymousCallbackQueue = null;
        }
    }

    /**
     * Polls connection for incoming data
     *
     * @throws \ErrorException
     */
    public function wait(): void
    {
        if ($this->channel !== null) {
            try {
                $this->channel->wait(null, true, 0.01);
            } catch (AMQPTimeoutException $ignore) {
            } catch (AMQPIOWaitException $io) {
            }
        }
    }
}
