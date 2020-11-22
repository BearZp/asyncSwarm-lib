<?php

namespace Lib\models\mappers;

interface MapperInterface
{
    /**
     * @param $model
     *
     * @return array
     */
    public function toArray(object $model): array;

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function fromArray(array $data);
}
