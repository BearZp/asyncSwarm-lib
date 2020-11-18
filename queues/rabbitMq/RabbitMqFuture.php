<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

declare(strict_types=1);

namespace Lib\queues\rabbitMq;

class RabbitMqFuture
{
    /**
     * @var RabbitMqRpcClient
     */
    private $provider;
    /**
     * @var boolean
     */
    private $done;
    /**
     * @var mixed
     */
    private $data;
    /**
     * @var \Exception|null
     */
    private $error;
    /**
     * @var float|null
     */
    private $timeoutAt;
    /**
     * @var float
     */
    private $startedAt;
    /**
     * @var float
     */
    private $doneAt;
    /**
     * @var string
     */
    private $queueName;

    /**
     * Constructor
     *
     * @param RabbitMqRpcClient $rpc
     * @param string $queueName
     * @param float|null $timeout
     */
    public function __construct(RabbitMqRpcClient $rpc, string $queueName, float $timeout = .0)
    {
        $this->done = false;
        $this->provider = $rpc;
        $this->startedAt = microtime(true);
        if ($timeout > 0) {
            $this->timeoutAt = $this->startedAt + $timeout;
        }
        $this->queueName = $queueName;
    }

    /**
     * Set current future value
     * @param string $data
     */
    public function complete(string $data)
    {
        if (!$this->done) {
            $this->done = true;
            $this->data = $data;
            $this->doneAt = microtime(true);
        }
    }

    /**
     * Returns scheduled future
     * @return string
     * @throws \Exception
     */
    public function get(): string
    {
        while (!$this->done) {
            if ($this->timeoutAt !== null && microtime(true) > $this->timeoutAt) {
                throw new \Exception('Time out');
            }
            $this->provider->wait();
        }
        return $this->data;
    }

    /**
     * Returns true if future finished with error
     * @internal For internal usage only, do not invoke this method
     * @return bool
     */
    public function hasError(): bool
    {
        return $this->error !== null;
    }

    /**
     * Returns future execution time
     * @internal For internal usage only, do not invoke this method
     * @return float
     */
    public function getLatency(): float
    {
        return $this->doneAt - $this->startedAt;
    }

    /**
     * Returns queue name
     * @internal For internal usage only, do not invoke this method
     * @return string
     */
    public function getQueueName(): string
    {
        return $this->queueName;
    }
}
