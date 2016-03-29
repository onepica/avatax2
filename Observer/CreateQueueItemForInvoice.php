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
use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Model\Queue;
use OnePica\AvaTax\Model\QueueFactory;
use OnePica\AvaTax\Model\QueueRepository;

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
     * Constructor
     *
     * @param Config          $config
     * @param QueueFactory    $queueFactory
     * @param QueueRepository $queueRepository
     */
    public function __construct(Config $config, QueueFactory $queueFactory, QueueRepository $queueRepository)
    {
        $this->config = $config;
        $this->queueFactory = $queueFactory;
        $this->queueRepository = $queueRepository;
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
        if ($invoice->getData(Queue::FLAG_CAN_ADD_TO_QUEUE)) {
            $queue = $this->queueFactory->create();
            $queue->setEntity($invoice);
            $queue->setType(Queue::TYPE_INVOICE);
            $queue->setStatus(Queue::STATUS_PENDING);
            $this->queueRepository->save($queue);
        }
    }
}
