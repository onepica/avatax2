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
namespace Astound\AvaTax\Model\Tool;

use Magento\Sales\Api\OrderRepositoryInterface;
use Astound\AvaTax\Model\Service\Avatax16;
use Astound\AvaTax\Model\Service\Exception\Commitfailure;
use Astound\AvaTax\Model\Service\Result\Creditmemo;
use Astound\AvaTax\Model\Service\Result\Invoice;
use Astound\AvaTax\Model\Service\Result\ResultInterface;
use Astound\AvaTax\Model\Service\ResolverInterface;
use Astound\AvaTax\Model\ServiceFactory;
use Magento\Sales\Model\Order\Invoice as OrderInvoice;
use Astound\AvaTax\Model\Queue;
use Astound\AvaTax\Helper\Data as DataHelper;

/**
 * Class Submit
 *
 * @method Avatax16 getService()
 * @package Astound\AvaTax\Model\Tool\Submit
 */
class Submit extends AbstractTool
{
    /**
     * Queue
     *
     * @var \Astound\AvaTax\Model\Queue
     */
    protected $queue;

    /**
     * Data helper
     *
     * @var DataHelper
     */
    protected $dataHelper;
    
    /**
     * Order repository
     *
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * Invoice constructor.
     *
     * @param \Astound\AvaTax\Model\Service\ResolverInterface $resolver
     * @param \Astound\AvaTax\Model\ServiceFactory            $serviceFactory
     * @param DataHelper                                      $dataHelper
     * @param OrderRepositoryInterface                        $orderRepository
     * @param Queue                                           $queue
     */
    public function __construct(
        ResolverInterface $resolver,
        ServiceFactory $serviceFactory,
        DataHelper $dataHelper,
        OrderRepositoryInterface $orderRepository,
        Queue $queue = null
    ) {
        parent::__construct($resolver, $serviceFactory);
        $this->dataHelper = $dataHelper;
        $this->orderRepository = $orderRepository;
        $this->queue = $queue;
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
     * Execute.
     * Process queue. Send request object to service
     *
     * @return ResultInterface
     * @throws \Astound\AvaTax\Model\Service\Exception\Unbalanced
     * @throws Commitfailure
     */
    public function execute()
    {
        $queueResult = $this->processQueue();
        //if successful
        if ($queueResult->getHasError()) {
            throw new Commitfailure($queueResult->getErrorsAsString());
        }

        $message = __($this->queue->getType())
            . ' #'
            . $queueResult->getDocumentCode()
            . ' '
            . __('was saved to AvaTax');

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->orderRepository->get($this->queue->getOrderId());
        $this->dataHelper->addStatusHistoryCommentToOrder($order, $message);

        /** @var Invoice $queueResult */
        $totalTax = $queueResult->getTotalTax();
        if (!$this->isQueueTaxSameAsResponseTax($this->queue->getTotalTaxAmount(), $totalTax)) {
            throw new \Astound\AvaTax\Model\Service\Exception\Unbalanced(
                'Collected: ' . $this->queue->getTotalTaxAmount() . ', Actual: ' . $totalTax
            );
        }

        return $queueResult;
    }

    /**
     * Get service object
     *
     * @return mixed
     */
    public function getServiceRequestObject()
    {
        return $this->getService()->getServiceRequestObject($this->queue);
    }

    /**
     * Process Queue
     *
     * @return Creditmemo|Invoice
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
