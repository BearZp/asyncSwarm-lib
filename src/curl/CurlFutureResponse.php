<?php

namespace Lib\curl;

use Lib\transport\FutureAnswerBundleInterface;
use Lib\transport\RpcClientInterface;

class CurlFutureResponse implements FutureAnswerBundleInterface
{
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
     * @var RpcClientInterface
     */
    private $provider;

    /**
     * Constructor
     *
     * @param RpcClientInterface $rpc
     * @param string $queueName
     * @param float|null $timeout
     */
    public function __construct(RpcClientInterface $rpc, string $queueName, float $timeout = .0)
    {
        $this->done = false;
        $this->startedAt = microtime(true);
        if ($timeout > 0) {
            $this->timeoutAt = $this->startedAt + $timeout;
        }
        $this->provider = $rpc;
        $this->queueName = $queueName;
    }

    /**
     * Set current future value
     * @param string $data
     * @return $this
     * @throws \Exception
     */
    public function complete(string $data): self
    {
        if (!$this->done) {
            $this->done = true;
            $this->data = $data;
            $this->doneAt = microtime(true);
            return $this;
        }
        throw new \Exception("Already completed. Current data is: $this->data, try to set new data: $data");
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
                throw new \Exception('Time out while getting response');
            }
            $this->provider->wait();
        }
        return $this->data;
    }

    /**
     * @param \Exception $error
     * @return $this
     */
    public function setError(\Exception $error): FutureAnswerBundleInterface
    {
        $this->done = true;
        $this->error = $error;

        return $this;
    }

    /**
     * @return \Exception
     */
    public function getError(): \Exception
    {
        while (!$this->done) {
            if ($this->timeoutAt !== null && microtime(true) > $this->timeoutAt) {
                $this->setError(new \Exception('Time out while getting response'));
            }
            $this->provider->wait();
        }

        return $this->error;
    }

    /**
     * Returns true if future finished with error
     * @return bool
     * @internal For internal usage only, do not invoke this method
     */
    public function hasError(): bool
    {
        return $this->error !== null;
    }

    /**
     * Returns future execution time
     * @return float
     * @internal For internal usage only, do not invoke this method
     */
    public function getLatency(): float
    {
        return $this->doneAt - $this->startedAt;
    }
}
