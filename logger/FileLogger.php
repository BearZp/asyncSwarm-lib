<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

declare(strict_types=1);

namespace Lib\logger;

use Psr\Log\LoggerInterface;

/**
 * Class UdpLogger
 * @package common\lib\logger
 */
class FileLogger extends AbstractLogger implements LoggerInterface
{
    /**
     * @var string
     */
    private $path;


    /**
     * @param string $path
     * @param string $limitLevel
     */
    public function __construct(string $path, string $limitLevel = '')
    {
        parent::__construct($limitLevel);
        $this->path = $path . date('Ymd') . '.log';
    }

    protected function write(string $paket): void
    {
        file_put_contents(
            $this->path,
            $paket . PHP_EOL,
            FILE_APPEND
        );
    }
}
