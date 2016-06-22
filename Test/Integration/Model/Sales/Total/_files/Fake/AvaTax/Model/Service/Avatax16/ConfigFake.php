<?php

namespace Fake\AvaTax\Model\Service\Avatax16;

use Fake\AvaTax\TaxServiceFake;
use OnePica\AvaTax16\Config as LibConfig;

/**
 * Class ConfigFake
 *
 * @package Astound\AvaTax\Model\Service\Avatax16
 */
class ConfigFake extends \Astound\AvaTax\Model\Service\Avatax16\Config
{
    /**
     * @param LibConfig $config
     *
     * @return TaxServiceFake
     */
    protected function getLibTaxService(LibConfig $config)
    {
        return new TaxServiceFake($config);
    }
}