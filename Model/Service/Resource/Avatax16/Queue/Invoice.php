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
namespace OnePica\AvaTax\Model\Service\Resource\Avatax16\Queue;

use OnePica\AvaTax\Api\Service\InvoiceResourceInterface;
use OnePica\AvaTax\Model\Queue;
use OnePica\AvaTax16\Document\Request;
use OnePica\AvaTax\Model\Service\Result\Invoice as InvoiceResult;

/**
 * Class Invoice
 *
 * @package OnePica\AvaTax\Model\Service\Resource\Avatax\Queue
 */
class Invoice extends AbstractQueue implements InvoiceResourceInterface
{
    /**
     * Get result object
     *
     * @return \OnePica\AvaTax\Model\Service\Result\Invoice
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
