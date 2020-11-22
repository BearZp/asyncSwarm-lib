<?php
declare(strict_types=1);

namespace Lib\models;

use Lib\models\mappers\MapperInterface;

abstract class AbstractModel implements ModelInterface
{
    /** @var MapperInterface */
    protected $mapper;

    /**
     * @return array
     * @throws \Exception
     */
    public function toArray(): array
    {
        if ($this->mapper instanceof MapperInterface) {
            return $this->mapper->toArray($this);
        }
        throw new \Exception('Try to transform model to array without saving', 500);
    }
}
