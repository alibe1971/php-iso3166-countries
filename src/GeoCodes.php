<?php

namespace Alibe\GeoCodes;

use Alibe\GeoCodes\Lib\BaseCode;

class GeoCodes extends BaseCode
{
    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setConfig();
        $this->reset();
    }
}
