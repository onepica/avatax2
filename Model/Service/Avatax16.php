<?php
/**
 * Astound_AvaTax
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0),
 * a copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Astound
 * @package    Astound_AvaTax
 * @author     Astound Codemaster <codemaster@astoundcommerce.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Model\Service;

use Astound\AvaTax\Model\Service\Resource\Avatax16\Calculation;
use Astound\AvaTax\Model\Service\Resource\Avatax16\Ping;
use Astound\AvaTax\Model\Service\Resource\Avatax16\Queue\Creditmemo;
use Astound\AvaTax\Model\Service\Resource\Avatax16\Queue\Invoice;
use Astound\AvaTax\Model\Service\Resource\Avatax16\Validation;

/**
 * Class Avatax
 *
 * @package Astound\AvaTax\Model\Service
 */
class Avatax16 extends AbstractService
{
    /**
     * Get ping resource class
     *
     * @return string
     */
    public function getPingResourceClass()
    {
        return Ping::class;
    }

    /**
     * Get invoice resource class
     *
     * @return string
     */
    public function getInvoiceResourceClass()
    {
        return Invoice::class;
    }

    /**
     * Get validation resource class
     *
     * @return string
     */
    public function getValidationResourceClass()
    {
        return Validation::class;
    }

    /**
     * Get creditmemo resource class
     *
     * @return string
     */
    public function getCreditmemoResourceClass()
    {
        return Creditmemo::class;
    }

    /**
     * Get calculation resource class
     *
     * @return string
     */
    public function getCalculationResourceClass()
    {
        return Calculation::class;
    }
}
