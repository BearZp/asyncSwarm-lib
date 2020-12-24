<?php
declare(strict_types=1);

namespace Lib\models;

trait FutureObjectTrait
{
    /** @var int */
    private $sleepMicroTime = 1;

    /** @var bool */
    private $completed = false;

    /** @var callable */
    protected $initFunction;

    /** @var mixed */
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
        while (! $this->isCompleted()) {
            \usleep($this->sleepMicroTime);
        }
        if ($this->object instanceof \Throwable) {
            throw $this->object;
        }

        return $this->object;
    }

    /**
     * @return bool
     */
    final public function isCompleted(): bool
    {
        if (! $this->completed):
            try {
                if ($this->object === null) {
                    $this->object = ($this->initFunction)();
                    $this->initFunction = null;
                    /** @noinspection NotOptimalIfConditionsInspection */
                    if ($this->object instanceof \Generator) {
                        $this->object->rewind();

                        return false;
                    }
                } elseif ($this->object instanceof \Generator) {
                    $this->object->next();
                    if ($this->object->valid()) {
                        return false;
                    }
                    $this->object = $this->object->getReturn();
                }
            } catch (\Throwable $e) {
                $this->object = $e;
            }
            $this->completed = true;
        endif;

        return true;
    }
}
