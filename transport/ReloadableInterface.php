<?php
/**
* Copyright (C) 2016 - 2020 Wallet Factory International LTD, Wallet Factory PL LTD or their affiliates.
* All rights reserved. <info@walletfactory.com>
* This file is part of Wallet Factory MFS Platform version 1.1.0MFS platform, project #36-16. Files can not
* be used, copied, modified and/or distributed without the express permission of Wallet Factory International LTD,
* Wallet Factory PL LTD or their affiliates. See conditions of permission in the license or source code agreement.
*/

declare(strict_types = 1);

namespace Lib\transport;

interface ReloadableInterface
{
    /**
     * Reloads configuration or restarts connection
     * @return void
     */
    public function reload();
}
