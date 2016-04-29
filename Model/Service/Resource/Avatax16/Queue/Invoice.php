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
namespace Astound\AvaTax\Model\Service\Resource\Avatax16\Queue;

use Astound\AvaTax\Model\Queue;
use OnePica\AvaTax16\Document\Request;
use Astound\AvaTax\Model\Service\Result\Invoice as InvoiceResult;

/**
 * Class Invoice
 *
 * @package Astound\AvaTax\Model\Service\Resource\Avatax\Queue
 */
class Invoice extends AbstractQueue
{
    /**
     * Get result object
     *
     * @return \Astound\AvaTax\Model\Service\Result\Invoice
     */
    protected function createResultObject()
    {
        return $this->objectManager->create(InvoiceResult::class);
    }

    /**
     * Get document code for object
     *
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $object
     * @return string
     */
    protected function getDocumentCodeForObject($object)
    {
        return self::DOCUMENT_CODE_INVOICE_PREFIX . $object->getIncrementId();
    }

    /**
     * Get if items is for credit
     *
     * @return bool
     */
    protected function isCredit()
    {
        return false;
    }
}
