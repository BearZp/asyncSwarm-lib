<?php declare(strict_types = 1);

namespace Lib\cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class LocaleCache extends ArrayAdapter implements CacheItemPoolInterface
{
    /** @var int */
    private $limitEntityAmount;

    /**
     * LocaleCache constructor.
     * @param int $defaultLifetime
     * @param int $limitEntityAmount
     */
    public function __construct(int $defaultLifetime, int $limitEntityAmount)
    {
        $this->limitEntityAmount = $limitEntityAmount;
        parent::__construct($defaultLifetime, false);
    }

    /**
     * @param CacheItemInterface $item
     *
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function save(CacheItemInterface $item)
    {
        $values = $this->getValues();
        if (count($values) > $this->limitEntityAmount) {
            $this->deleteItem(
                key($values)
            );
        }
        return parent::save($item);
    }

    /**
     * @return void
     */
    public function clearExpiredItems(): void
    {
        $this->clear();
    }
}
