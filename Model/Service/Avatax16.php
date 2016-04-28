<?php
/**
 * OnePica_AvaTax
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0),
 * a copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax\Model\Service;

use OnePica\AvaTax\Model\Service\Resource\Avatax16\Calculation;
use OnePica\AvaTax\Model\Service\Resource\Avatax16\Ping;
use OnePica\AvaTax\Model\Service\Resource\Avatax16\Queue\Creditmemo;
use OnePica\AvaTax\Model\Service\Resource\Avatax16\Queue\Invoice;
use OnePica\AvaTax\Model\Service\Resource\Avatax16\Validation;

/**
 * Class Avatax
 *
 * @package OnePica\AvaTax\Model\Service
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
