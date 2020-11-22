<?php
declare(strict_types=1);

/**
 * Created by PhpStorm for WL
 * User: BearZp aka Gulidov Vadim (gulidov.vadim@gmail.com)
 * Date: 14.03.19
 * Time: 11:10
 */

namespace Lib\types;

use Lib\types\base\TypeInterface;

class Url implements TypeInterface
{
    /** @var string */
    private $url;
    /** @var array */
    private $data;

    /**
     * Url constructor.
     * @param string $url
     * @param array $data
     */
    public function __construct(string $url, array $data = [])
    {
        $parseUrl = \parse_url($url);
        $url = '';
        if (isset($parseUrl['scheme']) && $parseUrl['scheme'] !== '') {
            $url .= $parseUrl['scheme'] . '://';
        }
        if ((isset($parseUrl['username']) && $parseUrl['username'] !== '')
            || (isset($parseUrl['pass']) && $parseUrl['pass'] !== '')
        ) {
            $url .= $parseUrl['username'] . ':' . $parseUrl['pass'] . '@';
        }
        if (isset($parseUrl['host']) && $parseUrl['host'] !== '') {
            $url .= $parseUrl['host'];
        }
        if (isset($parseUrl['port']) && $parseUrl['port'] !== '') {
            $url .= ':' . $parseUrl['port'];
        }
        if (isset($parseUrl['path']) && $parseUrl['path'] !== '') {
            $url .= $parseUrl['path'];
        }
        if (isset($parseUrl['query']) && $parseUrl['query'] !== '') {
            $query = [];
            parse_str($parseUrl['query'], $query);
            $data = array_replace($query, $data);
        }
        $this->url = $url;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        $url = $this->url;
        if (\count($this->data) > 0) {
            $url .= '?' . http_build_query($this->data);
        }
        return $url;
    }

    /**
     * @param TypeInterface $obj
     * @return bool
     */
    public function isEqual(TypeInterface $obj): bool
    {
        return $obj instanceof static and $obj->toString() === $this->toString();
    }
}
