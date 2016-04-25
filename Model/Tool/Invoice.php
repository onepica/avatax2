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
 * @author     OnePica Codemaster <codemaster@onepica.com>
 * @copyright  Copyright (c) 2016 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax\Model\Tool;

use OnePica\AvaTax\Model\Service\Avatax16;
use OnePica\AvaTax\Model\Service\Result\ResultInterface;

/**
 * Class Invoice
 *
 * @method Avatax16 getService()
 * @package OnePica\AvaTax\Model\Tool
 */
class Invoice extends AbstractQueueTool
{
    /**
     * Get Invoice Service Request Object
     *
     * @return mixed
     */
    public function getInvoiceServiceRequestObject()
    {
        return $this->getService()->getInvoiceServiceRequestObject($this->queueObject);
    }

    /**
     * Process Queue
     *
     * @return ResultInterface
     */
    protected function processQueue()
    {
        return $this->getService()->submit($this->queue);
    }

    /**
     * Is queue tax same as response tax
     *
     * @param float $queueTax
     * @param float $responseTax
     *
     * @return bool
     */
    protected function isQueueTaxSameAsResponseTax($queueTax, $responseTax)
    {
        return $queueTax == $responseTax;
    }
}
