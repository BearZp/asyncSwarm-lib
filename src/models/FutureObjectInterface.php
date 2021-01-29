<?php declare(strict_types = 1);

namespace Lib\models;

interface FutureObjectInterface
{
    /**
     * @return bool
     */
    public function isCompleted(): bool;

    /**
     * @return mixed
     */
    public function get();
}
