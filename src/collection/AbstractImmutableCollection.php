<?php declare(strict_types=1);

namespace Gronvi\FutureObject\Collection;

use ArrayAccess;
use Countable;
use lib\models\FutureObjectInterface;
use lib\models\FutureObjectTrait;
use InvalidArgumentException;
use Iterator;
use LogicException;
use OutOfBoundsException;

abstract class AbstractImmutableCollection implements Countable, ArrayAccess, Iterator, FutureObjectInterface
{
    use FutureObjectTrait {
        get as futureObjectTraitGet;
    }

    /** @var null|mixed[] */
    protected $collection;

    protected $firstItemKey;

    /**
     * AbstractImmutableCollection constructor.
     * @param array|self|callable $data
     * @throws \Throwable
     */
    public function __construct($data)
    {
        if (is_callable($data)) {
            $this->initFunction = $data;
        } elseif (is_array($data)) {
            $this->object = [];
            $this->addData($data);
        } elseif ($data instanceof self) {
            if ($data->isCompleted()) {
                $this->addData($data);
                $this->object = [];
            } else {
                $this->initFunction = static function () use ($data) {
                    if ($data instanceof FutureObjectInterface) {
                        while (!$data->isCompleted()) {
                            yield;
                        }
                    }
                    return $data;
                };
            }
        } else {
            throw new InvalidArgumentException(
                'Argument $data must be array, collection or callable. Received: '
                . (is_object($data) ? get_class($data) : gettype($data))
            );
        }
    }

    /**
     * @return self
     */
    public function get(): self
    {
        $this->initCollection();
        return $this;
    }

    /**
     * @return int
     * @throws InvalidArgumentException
     */
    public function count(): int
    {
        $this->initCollection();
        return count($this->collection);
    }

    /**
     * @return mixed
     */
    public function getFirstItem()
    {
        $this->initCollection();
        if ($this->firstItemKey === null) {
            throw new LogicException('Collection is empty');
        }
        return $this->collection[$this->firstItemKey];
    }

    /**
     * @return bool
     * @throws InvalidArgumentException
     */
    public function isEmpty(): bool
    {
        $this->initCollection();
        return count($this->collection) === 0;
    }

    /**
     * @param int $offset
     * @return bool
     * @throws InvalidArgumentException
     */
    public function offsetExists($offset): bool
    {
        $this->initCollection();
        return isset($this->collection[$offset]);
    }

    /**
     *
     * @param int $offset
     * @return mixed
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new OutOfBoundsException('Unable to find value with key ' . $offset);
        }
        return $this->collection[$offset];
    }

    /**
     * @param string|int $offset
     * @param mixed $value
     * @return void
     * @throws LogicException Always throws an exception
     */
    public function offsetSet($offset, $value): void
    {
        throw new LogicException('Unable to change contents of immutable collection');
    }

    /**
     * @param string|int $offset
     * @return void
     * @throws LogicException Always throws an exception
     */
    public function offsetUnset($offset):void
    {
        throw new LogicException('Unable to change contents of immutable array');
    }

    /**
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function current()
    {
        $this->initCollection();
        return current($this->collection);
    }

    /**
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function next()
    {
        $this->initCollection();
        return next($this->collection);
    }

    /**
     * @return int
     * @throws InvalidArgumentException
     */
    public function key(): int
    {
        $this->initCollection();
        return key($this->collection);
    }

    /**
     * @return bool
     * @throws InvalidArgumentException
     */
    public function valid(): bool
    {
        $this->initCollection();
        return key($this->collection) !== null;
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     */
    public function rewind(): void
    {
        $this->initCollection();
        reset($this->collection);
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public function toArray(): array
    {
        $this->initCollection();
        return $this->collection;
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     */
    final public function initCollection(): void
    {
        if ($this->collection === null) {
            $this->addData($this->futureObjectTraitGet());
            $this->object = [];
        }
        if ($this->firstItemKey === null && count($this->collection) !== 0) {
            reset($this->collection);
            $this->firstItemKey = key($this->collection);
        }
    }

    /**
     * @param $list
     * @throws InvalidArgumentException
     * @return void
     */
    protected function addData($list): void
    {
        if (is_array($list)) {
            $this->collection = [];
            foreach ($list as $item) {
                $this->validate($item);
                $this->collection[] = $item;
            }
        } else {
            if (!($list instanceof static)) {
                $type = is_object($list) ? get_class($list) : gettype($list);
                throw new InvalidArgumentException(
                    'Argument $list must be instance of ' . static::class
                    . " Variable of type `{$type}` supplied instead"
                );
            }
            $this->collection = $list->toArray();
        }
    }

    /**
     * @param $item
     */
    protected function addItem($item): void
    {
        if ($this->collection === null) {
            $this->collection = [];
        }
        $this->validate($item);
        $this->collection[] = $item;
        if ($this->firstItemKey === null) {
            $this->firstItemKey = 0;
        }
    }

    /**
     * @param mixed $value
     * @return void
     * @throws InvalidArgumentException
     */
    abstract public function validate($value): void;

    /**
     * @param AbstractImmutableCollection $collection
     * @return mixed
     */
    public function withCollection($collection)
    {
        if (!($collection instanceof static)) {
            $type = is_object($collection) ? get_class($collection) : gettype($collection);
            throw new InvalidArgumentException(
                'Argument $collection must be instance of ' . static::class
                . " Variable of type `{$type}` supplied instead"
            );
        }
        $this->get();
        $obj = clone $this;
        $obj->collection = array_merge($this->toArray(), $collection->toArray());
        return $obj;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function withValue($value)
    {
        $this->validate($value);
        $this->get();
        $obj = clone $this;
        $obj->collection[] = $value;
        return $obj;
    }
}
