<?php

namespace Alibe\GeoCodes;

use Alibe\GeoCodes\Lib\BaseCode;
use Alibe\GeoCodes\Lib\CodesCountries;
use Alibe\GeoCodes\Lib\CodesCurrencies;
use Alibe\GeoCodes\Lib\CodesGeoSets;

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

    public function countries(): CodesCountries
    {
        return new CodesCountries($this->getConfig(), $this->getInstanceLanguage());
    }

    public function geoSets(): CodesGeoSets
    {
        return new CodesGeoSets($this->getConfig(), $this->getInstanceLanguage());
    }

    public function geoCurrencies(): CodesCurrencies
    {
        return new CodesCurrencies($this->getConfig(), $this->getInstanceLanguage());
    }
}
