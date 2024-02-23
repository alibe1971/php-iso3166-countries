<?php

namespace Alibe\GeoCodes;

use Alibe\GeoCodes\Lib\BaseCode;
use Alibe\GeoCodes\Lib\CodesCountries;

class GeoCodes extends BaseCode
{
    private CodesCountries $codesCountries;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setConfig();
        $this->reset();
    }

    public function countries()
    {
    }
}
