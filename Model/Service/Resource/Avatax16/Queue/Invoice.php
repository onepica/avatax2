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
     * Get Invoice Service Request Object
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return mixed
     */
    public function getInvoiceServiceRequestObject(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $store = $invoice->getStore();
        // Copy Avatax data from order items to invoice items, because only order items contains this data
        $this->copyAvataxDataFromOrderItemsToObjectItems($invoice);
        $this->dataSource->initAvataxData($invoice->getItems(), $store);
        $this->initRequest($invoice);
        return $this->request;
    }

    /**
     * Get result object
     *
     * @return \OnePica\AvaTax\Model\Service\Result\Invoice
     */
    protected function createResultObject()
    {
        return $this->objectManager->create(InvoiceResult::class);
    }
}
