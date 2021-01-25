<?php


namespace Lib\curl;


use Lib\transport\FutureAnswerBundleInterface;
use Lib\transport\HttpConnectionProvider;
use Lib\transport\RpcClientInterface;

class CurlRpcClient implements RpcClientInterface
{
    /**
     * @var HttpConnectionProvider
     */
    private $provider;

    /**
     * HttpSwooleClient constructor.
     * @param HttpConnectionProvider $connection
     */
    public function __construct(HttpConnectionProvider $connection, bool $durable = false)
    {
        $this->provider = $connection;
    }

    /**
     * @param string $path
     * @param string $body
     * @param array $meta
     * @param float|null $timeout
     * @param bool $useRandomResponseQueue
     * @return FutureAnswerBundleInterface
     * @throws \Exception
     */
    public function call(
        string $path,
        string $body,
        array $meta = [],
        float $timeout = null,
        bool $useRandomResponseQueue = false
    ): FutureAnswerBundleInterface {

        $resp = new CurlFutureResponse($this, '', $timeout);


        $request = new \Lib\curl\Request($this->provider->getUrl());
        $request->getOptions()->set(CURLOPT_TIMEOUT, 5)->set(CURLOPT_RETURNTRANSFER, true);

        $request->addListener('complete', function (\Lib\curl\Event $event) use ($resp) {
            $resp->complete($event->response->getContent());
        });
        $request->socketPerform();

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