<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 12:41
 */

namespace App\lib\logger;

use Psr\Log\LoggerInterface;

trait LogReferenceTrait
{
    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
