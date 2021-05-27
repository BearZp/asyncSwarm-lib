<?php

namespace Lib\models;

use Lib\models\mappers\MapperInterface;

interface ModelInterface
{
    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * @return MapperInterface
     */
    public function getMapper(): MapperInterface;
}
