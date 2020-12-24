<?php

namespace Lib\transport;

interface RpcClientInterface
{
    /**
     * Call remote method with name $path and parameters $body
     * @param string $path
     * @param string $body
     * @param array $meta
     * @param float|null $timeout
     * @param bool $useRandomResponseQueue
     * @return FutureAnswerBundleInterface
     */
    public function call(
        string $path,
        string $body,
        array $meta = [],
        float $timeout = null,
        bool $useRandomResponseQueue = false
    ): FutureAnswerBundleInterface;

    /**
     * Wait for result
     */
    public function wait(): void;
}