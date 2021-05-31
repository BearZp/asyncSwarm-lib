<?php declare(strict_types = 1);

namespace Lib\models;

trait FutureObjectTrait
{
    /** @var int */
    private $sleepMicroTime = 1;
    /** @var bool */
    private $completed = false;
    /** @var callable */
    protected $initFunction;
    /** @var object */
    protected $object;

    /**
     * @param callable $initFunction
     */
    public function __construct(callable $initFunction)
    {
        $this->initFunction = $initFunction;
    }

    /**
     * @return mixed
     * @throws \Throwable
     */
    public function get()
    {
        while (!$this->isCompleted()) {
            usleep(1);
        }
        if ($this->object instanceof \Throwable) {
            throw $this->object;
        }
        return $this->object;
    }

    /**
     * @return bool
     * @throws \Throwable
     */
    final public function isCompleted(): bool
    {
        if (!$this->completed) {
            try {
                if ($this->initFunction !== null) {
                    $this->object = ($this->initFunction)();
                    $this->initFunction = null;
                    if ($this->object instanceof \Generator) {
                        $this->object->rewind();
                        if ($this->object->valid()) {
                            return false;
                        }
                        $this->object = $this->object->getReturn();
                    }
                }
                if ($this->object instanceof \Generator) {
                    $this->object->next();
                    if ($this->object->valid()) {
                        return false;
                    }
                    $this->object = $this->object->getReturn();
                }
                if ($this->object instanceof FutureObjectInterface) {
                    if (!$this->object->isCompleted()) {
                        return false;
                    }
                    if ($this->object !== $this->object->get()) {
                        $this->object = $this->object->get();
                    }
                }
                if ($this->object instanceof \Generator) {
                    return false;
                }
                $this->completed = true;
            } catch (\Throwable $e) {
                $this->completed = true;
                $this->initFunction = null;
                $this->object = $e;
                throw $e;
            }
        }
        return true;
    }
}
