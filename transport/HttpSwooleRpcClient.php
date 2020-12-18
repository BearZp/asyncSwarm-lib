<?php

namespace Lib\transport;

use Swoole\Coroutine\HTTP\Client;

class HttpSwooleRpcClient implements RpcClientInterface
{
    /**
     * @var HttpConnectionProvider
     */
    private $provider;

    /**
     * HttpSwooleClient constructor.
     * @param HttpConnectionProvider $provider
     */
    public function __construct($connection, bool $durable = false)
    {
        $this->provider = $connection;
    }

    /**
     * @param string $path
     * @param string $body
     * @param array $meta
     * @param float|null $timeout
     * @param bool $useRandomResponseQueue
     * @return HttpSwooleFuture
     * @throws \Exception
     */
    public function call(
        string $path,
        string $body,
        array $meta = [],
        float $timeout = null,
        bool $useRandomResponseQueue = false
    ): FutureAnswerBundleInterface {

        $meta['Host'] = $meta['Host'] ?? $this->provider->getHost();
        $meta['User-Agent'] = $meta['User-Agent'] ?? 'Chrome/49.0.2587.3';
        $meta['Accept'] = $meta['Accept'] ?? 'text/html,application/xhtml+xml,application/xml';
        $meta['Accept-Encoding'] = $meta['Accept-Encoding'] ?? 'gzip';

        $resp = new HttpSwooleFuture($this, '', $timeout);
        Co\run(function() use ($resp, $path, $meta, $body) {
            $cli = new Client($this->provider->getHost(), $this->provider->getPort());
            $cli->setHeaders($meta);
            $cli->set([ 'timeout' => 1]);
            $cli->post('/' . $path, ['a'=> 123,'b'=>"hey"]); // $body
            $resp->complete($cli->body);
            $cli->close();
        });

        return $resp;
    }

    /**
     * Wait for result
     */
    public function wait(): void
    {
        usleep(1);
    }

}