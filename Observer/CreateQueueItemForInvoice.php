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
namespace OnePica\AvaTax\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Framework\ObjectManagerInterface;
use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Model\Queue;
use OnePica\AvaTax\Model\QueueFactory;
use OnePica\AvaTax\Model\QueueRepository;
use OnePica\AvaTax\Helper\Address as AddressHelper;
use OnePica\AvaTax\Model\Tool\Submit\Invoice as InvoiceTool;

/**
 * Class CreateQueueItemForInvoice
 *
 * @package OnePica\AvaTax\Observer
 */
class CreateQueueItemForInvoice implements ObserverInterface
{
    /**
     * Config helper
     *
     * @var \OnePica\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * Queue Factory
     *
     * @var \OnePica\AvaTax\Model\QueueFactory
     */
    protected $queueFactory;

    /**
     * Queue Repository
     *
     * @var \OnePica\AvaTax\Model\QueueRepository
     */
    protected $queueRepository;

    /**
     * Address Helper
     *
     * @var AddressHelper
     */
    protected $addressHelper;

    /**
     * Object Manager
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param Config          $config
     * @param QueueFactory    $queueFactory
     * @param QueueRepository $queueRepository
     * @param AddressHelper   $addressHelper
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Config $config,
        QueueFactory $queueFactory,
        QueueRepository $queueRepository,
        AddressHelper $addressHelper,
        ObjectManagerInterface $objectManager
    ) {
        $this->config = $config;
        $this->queueFactory = $queueFactory;
        $this->queueRepository = $queueRepository;
        $this->addressHelper = $addressHelper;
        $this->objectManager = $objectManager;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();
        $totalTax = $this->getTotalTax($invoice);

        if ($invoice->getData(Queue::FLAG_CAN_ADD_TO_QUEUE)
            && $this->addressHelper->isObjectActionable($invoice)
        ) {
            $queue = $this->queueFactory->create();
            $queue->setEntity($invoice);
            $queue->setType(Queue::TYPE_INVOICE);
            $queue->setStatus(Queue::STATUS_PENDING);
            $queue->setTotalTaxAmount($totalTax);

            // save request object with data to use during queue processing
            $requestObjectSerialized = serialize($this->getRequestObject($invoice));
            $queue->setRequestData($requestObjectSerialized);

            $this->queueRepository->save($queue);
        }
    }

    /**
     * Get total tax
     *
     * @param \Magento\Sales\Model\Order\Invoice $object
     * @return float
     */
    protected function getTotalTax($object)
    {
        $tax = (float)$object->getData('base_tax_amount');

        foreach ($object->getItems() as $item) {
            $tax += $item->getData('base_weee_tax_applied_row_amnt');
        }

        return $tax;
    }

    /**
     * Get request object
     *
     * @param Invoice $invoice
     * @return mixed
     */
    protected function getRequestObject(Invoice $invoice)
    {
        /** @var InvoiceTool $invoiceService */
        $invoiceService = $this->objectManager->get(InvoiceTool::class);
        $invoiceService->setQueueObject($invoice);
        
        return $invoiceService->getInvoiceServiceRequestObject();
    }
}
