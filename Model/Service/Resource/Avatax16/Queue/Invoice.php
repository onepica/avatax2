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

use OnePica\AvaTax\Api\ResultInterface;
use OnePica\AvaTax\Api\Service\InvoiceResourceInterface;
use OnePica\AvaTax\Model\Queue;
use OnePica\AvaTax16\Document\Request;

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
     * Init request
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    protected function initRequest($invoice)
    {
        $this->request = new Request();
        $header = $this->prepareHeaderForInvoice($invoice);
        $this->request->setHeader($header);

        $this->prepareLines($invoice);
        $this->request->setLines(array_values($this->lines));

        return $this;
    }

    /**
     * Prepare header
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return \OnePica\AvaTax16\Document\Request\Header
     */
    protected function prepareHeaderForInvoice($invoice)
    {
        $store = $invoice->getStore();
        $order = $invoice->getOrder();
        $shippingAddress = ($order->getShippingAddress()) ? $order->getShippingAddress() : $order->getBillingAddress();
        $invoiceDate = $this->convertGmtDate($invoice->getCreatedAt(), $store);
        $orderDate = $this->convertGmtDate($order->getCreatedAt(), $store);

        $header = parent::prepareHeader($store, $shippingAddress);
        $header->setDocumentCode($this->getInvoiceDocumentCode($invoice));
        $header->setTransactionDate($invoiceDate);
        $header->setTaxCalculationDate($orderDate);

        return $header;
    }

    /**
     * Prepare lines
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    protected function prepareLines($invoice)
    {
        $this->lines = [];
        $store = $invoice->getStore();
        $this->addLine($this->prepareShippingLine($store, $invoice, false), $this->getShippingSku($store));
        $this->addLine($this->prepareGwOrderLine($store, $invoice, false), $this->getGwOrderSku($store));
        $this->addLine($this->prepareGwPrintedCardLine($store, $invoice, false), $this->getGwPrintedCardSku($store));
        $this->addItemsLine($store, $invoice->getItems());

        return $this;
    }

    /**
     * Get document code for invoice
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return string
     */
    protected function getInvoiceDocumentCode($invoice)
    {
        return self::DOCUMENT_CODE_INVOICE_PREFIX . $invoice->getIncrementId();
    }

    /**
     * Execute.
     * Process queue for invoice. Send request object to service
     *
     * @param Queue $queue
     * @return ResultInterface
     */
    public function invoice(Queue $queue)
    {
        // TODO: Implement processQueue() method.
        return true;
    }
}
