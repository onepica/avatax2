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
namespace OnePica\AvaTax\Model\Tool\Submit;

use Magento\Sales\Api\OrderRepositoryInterface;
use OnePica\AvaTax\Model\Service\Exception\Commitfailure;
use OnePica\AvaTax\Model\Service\Result\Creditmemo;
use OnePica\AvaTax\Model\Service\Result\Invoice;
use OnePica\AvaTax\Model\Service\Result\ResultInterface;
use OnePica\AvaTax\Model\Service\ResolverInterface;
use OnePica\AvaTax\Model\ServiceFactory;
use Magento\Sales\Model\Order\Invoice as OrderInvoice;
use OnePica\AvaTax\Model\Queue;
use OnePica\AvaTax\Helper\Data as DataHelper;
use OnePica\AvaTax\Model\Tool\AbstractTool;

/**
 * Class AbstractSubmit
 *
 * @package OnePica\AvaTax\Model\Tool\Submit
 */
abstract class AbstractSubmit extends AbstractTool
{
    /**
     * Queue object: Invoice or creditmemo object
     *
     * @var \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo
     */
    protected $queueObject;

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
     * Order repository
     *
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * Invoice constructor.
     *
     * @param \OnePica\AvaTax\Model\Service\ResolverInterface $resolver
     * @param \OnePica\AvaTax\Model\ServiceFactory            $serviceFactory
     * @param DataHelper                                      $dataHelper
     * @param OrderRepositoryInterface     $orderRepository
     */
    public function __construct(
        ResolverInterface $resolver,
        ServiceFactory $serviceFactory,
        DataHelper $dataHelper,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($resolver, $serviceFactory);
        $this->dataHelper = $dataHelper;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Set queue object
     *
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $queueObject
     * @return $this
     */
    public function setQueueObject($queueObject)
    {
        $this->queueObject = $queueObject;

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
     * Execute.
     * Process queue. Send request object to service
     *
     * @return ResultInterface
     * @throws \OnePica\AvaTax\Model\Service\Exception\Unbalanced
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
        $order = $this->orderRepository->get($this->queue->getEntityId());
        $this->dataHelper->addStatusHistoryCommentToOrder($order, $message);

        /** @var Invoice $queueResult */
        $totalTax = $queueResult->getTotalTax();
        if (!$this->isQueueTaxSameAsResponseTax($this->queue->getTotalTaxAmount(), $totalTax)) {
            throw new \OnePica\AvaTax\Model\Service\Exception\Unbalanced(
                'Collected: ' . $this->queue->getTotalTaxAmount() . ', Actual: ' . $totalTax
            );
        }

        return $queueResult;
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
