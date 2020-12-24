<?php declare(strict_types = 1);

namespace Lib\transport;

class HttpConnectionProvider
{
    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var string */
    private $user;

    /** @var string */
    private $pass;

    /** @var string */
    private $scheme;

    /** @var string */
    private $url;
    /**
     * @var string
     */
    private $path;

    /**
     * HttpConnectionProvider constructor.
     * @param string $host
     * @param int $port
     * @param string $user
     * @param string $pass
     * @param string $scheme
     * @param string $path
     */
    public function __construct(
        string $host,
        int $port,
        string $user,
        string $pass,
        string $scheme,
        string $path
    ) {
        $this->host = $host;
        $this->port = $port ? $port : ($scheme === 'http' ? 80 : 443);
        $this->user = $user;
        $this->pass = $pass;
        $this->scheme = $scheme;
        $this->path = $path;

        $this->buildUrl();
    }

    public function getConnection()
    {
        return $this;
    }

    protected function buildUrl(): void
    {
        $this->url = $this->scheme . '://';
        if ($this->user !== '') {
            $this->url .= $this->user;
            if($this->pass !== '') {
                $this->url .= ':' . $this->pass;
            }
            $this->url .= '@';
        }
        $this->url .= $this->host;

        if ($this->port !== 0) {
            $this->url .= ':' . $this->port;
        }

        if ($this->path === '') {
            $this->path = '/';
        }

        $this->url .= $this->path;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPass(): string
    {
        return $this->pass;
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }
}