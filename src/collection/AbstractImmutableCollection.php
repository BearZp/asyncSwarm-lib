<?php
declare(strict_types=1);

namespace Lib\collection;

use Lib\exception\NotInstanceOfException;
use Lib\models\FutureObjectInterface;
use Lib\models\FutureObjectTrait;

abstract class AbstractImmutableCollection implements \Countable, \ArrayAccess, \Iterator, FutureObjectInterface
{
    use FutureObjectTrait {
        get as futureObjectTraitGet;
    }

    /** @var null|mixed[] */
    protected $collection;

    /**
     * @param array|callable $data
     * @throws \InvalidArgumentException
     */
    public function __construct($data)
    {
        if (\is_callable($data)) {
            $this->initFunction = $data;
        } elseif (\is_array($data)) {
            $this->initFunction = function () use ($data) {
                return $data;
            };
        } else {
            throw new \InvalidArgumentException(
                '$data',
                'Argument $data must be array or callable',
                \gettype($data)
            );
        }
    }

    /**
     * @return AbstractImmutableCollection
     */
    public function get(): AbstractImmutableCollection
    {
        $this->initCollection();
        return $this;
    }

    /**
     * @return int
     * @throws \InvalidArgumentException
     */
    public function count(): int
    {
        $this->initCollection();
        return \count($this->collection);
    }

    /**
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function isEmpty(): bool
    {
        $this->initCollection();
        return \count($this->collection) === 0;
    }

    /**
     * @param int $offset
     * @return bool
     * @throws \InvalidArgumentException
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
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new \OutOfBoundsException('Unable to find value with key ' . $offset);
        }
        return $this->collection[$offset];
    }

    /**
     * @param string|int $offset
     * @param mixed $value
     * @return void
     * @throws \LogicException Always throws an exception
     */
    public function offsetSet($offset, $value): void
    {
        throw new \LogicException('Unable to change contents of immutable collection');
    }

    /**
     * @param int $offset
     * @return void
     * @throws \LogicException Always throws an exception
     */
    public function offsetUnset($offset):void
    {
        throw new \LogicException('Unable to change contents of immutable array');
    }

    /**
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function current()
    {
        $this->initCollection();
        return current($this->collection);
    }

    /**
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function next()
    {
        $this->initCollection();
        return next($this->collection);
    }

    /**
     * @return int
     * @throws \InvalidArgumentException
     */
    public function key(): int
    {
        $this->initCollection();
        return key($this->collection);
    }

    /**
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function valid(): bool
    {
        $this->initCollection();
        return key($this->collection) !== null;
    }

    /**
     * @return void
     * @throws \InvalidArgumentException
     */
    public function rewind(): void
    {
        $this->initCollection();
        reset($this->collection);
    }

    /**
     * @return array
     * @throws \InvalidArgumentException
     */
    public function toArray(): array
    {
        $this->initCollection();
        return $this->collection;
    }

    /**
     * @return void
     * @throws \InvalidArgumentException
     */
    final public function initCollection(): void
    {
        if ($this->collection === null) {
            $this->addData($this->futureObjectTraitGet());
            $this->object = null;
        }
    }

    /**
     * @param array $list
     * @throws \InvalidArgumentException
     * @return void
     */
    protected function addData(array $list): void
    {
        $this->collection = [];
        foreach ($list as $item) {
            $this->validate($item);
            $this->collection[] = $item;
        }
    }

    /**
     * @param mixed $value
     * @return void
     * @throws \InvalidArgumentException
     */
    abstract public function validate($value): void;

    /**  */
    public function getClone()
    {
        $obj = $this->get();
        return clone $obj;
    }

    /**
     * @param AbstractImmutableCollection $collection
     * @return mixed
     */
    public function withCollection($collection)
    {
        if (!($collection instanceof static)) {
            throw new NotInstanceOfException(
                '$object',
                static::class,
                \is_object($collection) ? \get_class($collection) : \gettype($collection)
            );
        }
        return new static(\array_merge($this->toArray(), $collection->toArray()));
    }

    /**
     * @param $value
     * @return mixed
     */
    public function withValue($value)
    {
        $this->validate($value);
        $collectionArr = $this->toArray();
        $collectionArr[] = $value;
        return new static($collectionArr);
    }
}
