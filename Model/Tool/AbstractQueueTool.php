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

use OnePica\AvaTax\Model\Service\Result\ResultInterface;
use OnePica\AvaTax\Api\Service\ResolverInterface;
use OnePica\AvaTax\Model\ServiceFactory;
use Magento\Sales\Model\Order\Invoice as OrderInvoice;
use OnePica\AvaTax\Model\Queue;
use OnePica\AvaTax\Helper\Data as DataHelper;

/**
 * Class AbstractQueueTool
 *
 * @package OnePica\AvaTax\Model\Tool
 */
abstract class AbstractQueueTool extends AbstractTool
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
     * Invoice constructor.
     *
     * @param \OnePica\AvaTax\Api\Service\ResolverInterface $resolver
     * @param \OnePica\AvaTax\Model\ServiceFactory          $serviceFactory
     * @param DataHelper                                    $dataHelper
     */
    public function __construct(
        ResolverInterface $resolver,
        ServiceFactory $serviceFactory,
        DataHelper $dataHelper
    ) {
        parent::__construct($resolver, $serviceFactory);
        $this->dataHelper = $dataHelper;
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
     * @throws \OnePica\AvaTax\Model\Service\Exception\Commitfailure
     */
    public function execute()
    {
        $queueResult = $this->processQueue();
        //if successful
        if (!$queueResult->getHasError()) {
            $message = __($this->queue->getType())
                     . ' #'
                     . $queueResult->getDocumentCode()
                     . ' '
                     . __('was saved to AvaTax');

            $order = $this->queueObject->getOrder();
            $this->dataHelper->addStatusHistoryCommentToOrder($order, $message);

            $totalTax = $queueResult->getTotalTax();
            if (!$this->isQueueTaxSameAsResponseTax($this->queue->getTotalTaxAmount(), $totalTax)) {
                throw new \OnePica\AvaTax\Model\Service\Exception\Unbalanced(
                    'Collected: ' . $this->queue->getTotalTaxAmount() . ', Actual: ' . $totalTax
                );
            }
            //if not successful
        } else {
            throw new \OnePica\AvaTax\Model\Service\Exception\Commitfailure($queueResult->getErrorsAsString());
        }

        return $queueResult;
    }

    /**
     * Process Queue
     *
     * @return ResultInterface
     */
    abstract protected function processQueue();

    /**
     * Is queue tax same as response tax
     *
     * @param float $queueTax
     * @param float $responseTax
     * @return bool
     */
    abstract protected function isQueueTaxSameAsResponseTax($queueTax, $responseTax);
}
