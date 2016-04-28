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
 * @author     Astound Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Framework\ObjectManagerInterface;
use Astound\AvaTax\Helper\Config;
use Astound\AvaTax\Model\Queue;
use Astound\AvaTax\Model\QueueFactory;
use Astound\AvaTax\Model\QueueRepository;
use Astound\AvaTax\Helper\Address as AddressHelper;
use Astound\AvaTax\Model\Tool\Submit;

/**
 * Class CreateQueueItemForInvoice
 *
 * @package Astound\AvaTax\Observer
 */
class CreateQueueItemForInvoice implements ObserverInterface
{
    /**
     * Config helper
     *
     * @var \Astound\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * Queue Factory
     *
     * @var \Astound\AvaTax\Model\QueueFactory
     */
    protected $queueFactory;

    /**
     * Queue Repository
     *
     * @var \Astound\AvaTax\Model\QueueRepository
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
            $requestObjectSerialized = serialize($this->getRequestObject($queue));
            $queue->setRequestData($requestObjectSerialized);
            $queue->setData('sales_object', null);

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
     * @param Queue $queue
     * @return mixed
     */
    protected function getRequestObject(Queue $queue)
    {
        /** @var Submit $submit */
        $submit = $this->objectManager->get(Submit::class);
        $submit->setQueue($queue);

        return $submit->getServiceRequestObject();
    }
}
