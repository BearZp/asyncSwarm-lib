<?php

namespace Lib\transport;

interface FutureAnswerBundleInterface
{
    /**
     * FutureAnswerBandleInterface constructor.
     * @param RpcClientInterface $rpc
     * @param string $queueName
     * @param float $timeout
     */
    public function __construct(RpcClientInterface $rpc, string $queueName, float $timeout = .0);

    /**
     * Set current future value
     * @param string $data
     */
    public function complete(string $data);

    /**
     * Returns scheduled future
     * @return string
     * @throws \Exception
     */
    public function get(): string;

    /**
     * Setter for future finished error
     * @param \Exception $error
     * @return $this
     */
    public function setError(\Exception $error): self;

    /**
     * Return future finished error
     * @return \Exception
     * @throws \ErrorException
     */
    public function getError(): \Exception;

    /**
     * Returns true if future finished with error
     * @internal For internal usage only, do not invoke this method
     * @return bool
     */
    public function hasError(): bool;
}