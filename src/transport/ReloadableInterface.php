<?php
/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 22.10.20
 * Time: 13:18
 */

declare(strict_types=1);

namespace Lib\transport;

interface ReloadableInterface
{
    /**
     * Reloads configuration or restarts connection
     * @return void
     */
    public function reload();
}
