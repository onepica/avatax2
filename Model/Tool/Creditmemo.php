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

use OnePica\AvaTax\Api\ResultInterface;

/**
 * Class Creditmemo
 *
 * @package OnePica\AvaTax\Model\Tool
 */
class Creditmemo extends AbstractQueueTool
{
    /**
     * Get Creditmemo Service Request Object
     *
     * @return mixed
     */
    public function getCreditmemoServiceRequestObject()
    {
        return $this->getService()->getCreditmemoServiceRequestObject($this->queueObject);
    }

    /**
     * Process Queue
     *
     * @return ResultInterface
     */
    protected function processQueue()
    {
        return $this->getService()->creditmemo($this->queue);
    }

    /**
     * Is queue tax same as response tax
     *
     * @param float $queueTax
     * @param float $responseTax
     * @return bool
     */
    protected function isQueueTaxSameAsResponseTax($queueTax, $responseTax)
    {
        return $queueTax == (-1) * $responseTax;
    }
}
