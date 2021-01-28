<?php declare(strict_types=1);

namespace Lib\types\base;

/**
 * Class Enum
 * @package common\lib\types
 */
abstract class EnumType implements TypeInterface
{
    /** @var array -- \ReflectionClass cache */
    private static $map = [];

    /** @var string */
    private $key;

    /** @var mixed */
    private $value;

    /** @var mixed */
    private const VALUES_INDEX = 0,
        KEYS_INDEX = 1;

    /**
     * Creates new Enum instance
     *
     * @param mixed $value
     *
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    public function __construct($value)
    {
        if (\is_object($value) && $value instanceof static) {
            $value = $value->value;
        }

        if (! static::isValid($value)) {
            throw new \InvalidArgumentException(
                'Unable to create enum ' . static::class . ' with '
                . (\is_scalar($value) ? 'value ' . $value : 'not scalar value')
            );
        }
        $keysMap = static::keysMap();
        $this->value = $value;
        $this->key = $keysMap[$value];
    }

    /**
     * Reads constants list using reflection
     * @throws \ReflectionException
     */
    private static function init(): void
    {
        $thisClassName = static::class;
        $thisClassConstants = (new \ReflectionClass($thisClassName))->getConstants();

        self::$map[$thisClassName] = [
            self::VALUES_INDEX => $thisClassConstants,
            self::KEYS_INDEX => \array_flip($thisClassConstants),
        ];
    }

    /**
     * Returns linear list
     * @return mixed[]
     * @throws \ReflectionException
     */
    public static function values(): array
    {
        return \array_values(static::valuesMap());
    }

    /**
     * Returns associative array, where keys are constant names and values are constant values
     * @return array
     * @throws \ReflectionException
     */
    public static function valuesMap(): array
    {
        $thisClassName = static::class;
        if (! isset(self::$map[$thisClassName])) {
            static::init();
        }

        return self::$map[$thisClassName][self::VALUES_INDEX];
    }

    /**
     * Returns associative array, where keys are constant values and values are constant names
     * @return array
     * @throws \ReflectionException
     */
    public static function keysMap(): array
    {
        $thisClassName = static::class;
        if (! isset(self::$map[$thisClassName])) {
            static::init();
        }

        return self::$map[$thisClassName][self::KEYS_INDEX];
    }

    /**
     * Returns true if provided value is value Enum constant
     *
     * @param mixed $value
     *
     * @return bool
     * @throws \ReflectionException
     */
    public static function isValid($value): bool
    {
        return \in_array($value, static::valuesMap(), true);
    }

    /**
     * Returns constant name
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns true if $to contains same constant value or if $to contains same type Enum with same value
     *
     * @param TypeInterface $to
     *
     * @return bool
     */
    public function isEqual(TypeInterface $to): bool
    {
        if ($to === null) {
            return false;
        }
        if (\is_object($to) && $to instanceof static) {
            return $this->value === $to->value;
        }

        return $this->value === $to;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return (string)$this->getValue();
    }
}
