<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

declare(strict_types=1);

namespace Lib\queues\rabbitMq;

/**
 * Class RabbitMqMsg - simple typization class
 */
class RabbitMqMsg
{
    /**
     * @var string
     */
    private $data;

    /**
     * RabbitMqMsg constructor.
     * @param string $data
     */
    public function __construct(string $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }
}
