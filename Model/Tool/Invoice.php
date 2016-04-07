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
use OnePica\AvaTax\Api\Service\ResolverInterface;
use OnePica\AvaTax\Model\ServiceFactory;
use Magento\Sales\Model\Order\Invoice as OrderInvoice;
use OnePica\AvaTax\Model\Queue;
use OnePica\AvaTax\Helper\Data as DataHelper;

/**
 * Class Invoice
 *
 * @package OnePica\AvaTax\Model\Tool
 */
class Invoice extends AbstractTool
{
    /**
     * Invoice
     *
     * @var \Magento\Sales\Model\Order\Invoice
     */
    protected $invoice;

    /**
     * Queue
     *
     * @var \OnePica\AvaTax\Model\Queue
     */
    protected $queue;

    /**
     * Data helper
     *
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * Invoice constructor.
     *
     * @param \OnePica\AvaTax\Api\Service\ResolverInterface $resolver
     * @param \OnePica\AvaTax\Model\ServiceFactory          $serviceFactory
     * @param \Magento\Sales\Model\Order\Invoice            $invoice
     * @param DataHelper                                    $dataHelper
     */
    public function __construct(
        ResolverInterface $resolver,
        ServiceFactory $serviceFactory,
        OrderInvoice $invoice,
        DataHelper $dataHelper
    ) {
        parent::__construct($resolver, $serviceFactory);
        $this->setInvoice($invoice);
        $this->dataHelper = $dataHelper;
    }

    /**
     * Set invoice
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function setInvoice(OrderInvoice $invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Set queue
     *
     * @param Queue $queue
     * @return $this
     */
    public function setQueue(Queue $queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * Get Invoice Service Request Object
     *
     * @return mixed
     */
    public function getInvoiceServiceRequestObject()
    {
        return $this->getService()->getInvoiceServiceRequestObject($this->invoice);
    }

    /**
     * Execute.
     * Process queue for invoice. Send request object to service
     *
     * @return ResultInterface
     * @throws \OnePica\AvaTax\Model\Service\Exception\Unbalanced
     * @throws \OnePica\AvaTax\Model\Service\Exception\Commitfailure
     */
    public function execute()
    {
        $invoiceResult =  $this->getService()->invoice($this->queue);

        //if successful
        if (!$invoiceResult->getHasError()) {
            $message = __('Invoice #%1 was saved to AvaTax', $invoiceResult->getDocumentCode());
            $order = $this->invoice->getOrder();
            $this->dataHelper->addStatusHistoryCommentToOrder($order, $message);

            $totalTax = $invoiceResult->getTotalTax();
            if ($totalTax != $this->invoice->getBaseTaxAmount()) {
                throw new \OnePica\AvaTax\Model\Service\Exception\Unbalanced(
                    'Collected: ' . $this->invoice->getBaseTaxAmount() . ', Actual: ' . $totalTax
                );
            }
            //if not successful
        } else {
            $messages = $invoiceResult->getErrors();
            throw new \OnePica\AvaTax\Model\Service\Exception\Commitfailure(implode(' // ', $messages));
        }

        return $invoiceResult;
    }
}
