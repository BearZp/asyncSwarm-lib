<?php
declare(strict_types=1);

namespace Lib\exception;

class NotInstanceOfException extends \InvalidArgumentException
{
    /**
     * NotInstanceOfException constructor.
     * @param string $argumentName
     * @param string $className
     * @param string|null $type
     */
    public function __construct(string $argumentName, string $className, string $type)
    {
        $message = sprintf('Argument `%s` must be instance of `%s`.', $argumentName, $className)
            . " Variable of type `{$type}` supplied instead";
        parent::__construct($message);
    }
}
