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
namespace OnePica\AvaTax\Model\Queue;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Stdlib\DateTime;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\CreditmemoRepository;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\InvoiceRepository;
use OnePica\AvaTax\Api\QueueManagerInterface;
use OnePica\AvaTax\Api\QueueRepositoryInterface;
use OnePica\AvaTax\Model\Queue;
use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Model\Tool\Invoice as InvoiceServiceTool;
use OnePica\AvaTax\Model\Tool\Creditmemo as CreditmemoServiceTool;

/**
 * Class Manager
 *
 * @package OnePica\AvaTax\Model\Queue
 */
class Manager implements QueueManagerInterface
{
    /**
     * Queue repository
     *
     * @var QueueRepositoryInterface
     */
    protected $queueRepository;

    /**
    * @var SearchCriteriaBuilder
    */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * Config helper
     *
     * @var \OnePica\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * DateTime model
     *
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @var InvoiceServiceTool
     */
    protected $invoiceServiceTool;

    /**
     * @var CreditmemoServiceTool
     */
    protected $creditmemoServiceTool;

    /**
     * Creditmemo repository
     *
     * @var CreditmemoRepository
     */
    protected $creditmemoRepository;

    /**
     * Invoice repository
     *
     * @var InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * Constructor.
     *
     * @param QueueRepositoryInterface $queueRepository
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder
     * @param FilterBuilder            $filterBuilder
     * @param Config                   $config
     * @param DateTime                 $dateTime
     * @param InvoiceServiceTool       $invoiceServiceTool
     * @param CreditmemoServiceTool    $creditmemoServiceTool
     * @param CreditmemoRepository     $creditmemoRepository
     * @param InvoiceRepository        $invoiceRepository
     * @param SortOrderBuilder         $sortOrderBuilder
     */
    public function __construct(
        QueueRepositoryInterface $queueRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        Config $config,
        DateTime $dateTime,
        InvoiceServiceTool $invoiceServiceTool,
        CreditmemoServiceTool $creditmemoServiceTool,
        CreditmemoRepository $creditmemoRepository,
        InvoiceRepository $invoiceRepository
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->queueRepository = $queueRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->config = $config;
        $this->dateTime = $dateTime;
        $this->invoiceServiceTool = $invoiceServiceTool;
        $this->creditmemoServiceTool = $creditmemoServiceTool;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * Clear queue processed items
     *
     * @return $this
     */
    public function clear()
    {
        $filters = array_map(function ($value) {
            return $this->filterBuilder
                ->setConditionType('in')
                ->setField(Queue::STATUS)
                ->setValue($value)
                ->create();
        }, [[Queue::STATUS_FAILED, Queue::STATUS_UNBALANCED, Queue::STATUS_COMPLETE]]);
        $this->searchCriteriaBuilder->addFilters($filters);
        $items = $this->queueRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems();

        foreach ($items as $item) {
            $this->queueRepository->delete($item);
        }

        return $this;
    }

    /**
     * Process queue
     *
     * @return $this
     */
    public function processQueue()
    {
        $this->cleanCompleted()
            ->cleanFailed()
            ->cleanUnbalanced()
            ->processItems();

        return $this;
    }

    /**
     * Delete any queue items that have been completed
     *
     * @return $this
     */
    protected function cleanCompleted()
    {
        $days = $this->config->getQueueSuccessLifetime();
        $filters[] = $this->filterBuilder
                ->setConditionType('eq')
                ->setField(Queue::STATUS)
                ->setValue(Queue::STATUS_COMPLETE)
                ->create();
        $filters[] = $this->filterBuilder
            ->setConditionType('lt')
            ->setField(Queue::CREATED_AT)
            ->setValue($this->dateTime->gmDate('Y-m-d H:i:s', strtotime('-' . $days . ' days')))
            ->create();
        $this->searchCriteriaBuilder->addFilters($filters);
        $items = $this->queueRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems();

        // delete items
        foreach ($items as $item) {
            $this->queueRepository->delete($item);
        }

        return $this;
    }

    /**
     * Delete any queue items that have been failed
     *
     * @return $this
     */
    protected function cleanFailed()
    {
        $days = $this->config->getQueueFailedLifetime();
        $filters[] = $this->filterBuilder
            ->setConditionType('eq')
            ->setField(Queue::STATUS)
            ->setValue(Queue::STATUS_FAILED)
            ->create();
        $filters[] = $this->filterBuilder
            ->setConditionType('lt')
            ->setField(Queue::CREATED_AT)
            ->setValue($this->dateTime->gmDate('Y-m-d H:i:s', strtotime('-' . $days . ' days')))
            ->create();
        $this->searchCriteriaBuilder->addFilters($filters);
        $items = $this->queueRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems();

        // delete items
        foreach ($items as $item) {
            $this->queueRepository->delete($item);
        }

        return $this;
    }

    /**
     * Delete any queue items that have been failed
     *
     * @return $this
     */
    protected function cleanUnbalanced()
    {
        $days = $this->config->getQueueFailedLifetime();
        $filters[] = $this->filterBuilder
            ->setConditionType('eq')
            ->setField(Queue::STATUS)
            ->setValue(Queue::STATUS_UNBALANCED)
            ->create();
        $filters[] = $this->filterBuilder
            ->setConditionType('lt')
            ->setField(Queue::CREATED_AT)
            ->setValue($this->dateTime->gmDate('Y-m-d H:i:s', strtotime('-' . $days . ' days')))
            ->create();
        $this->searchCriteriaBuilder->addFilters($filters);
        $items = $this->queueRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems();

        // delete items
        foreach ($items as $item) {
            $this->queueRepository->delete($item);
        }

        return $this;
    }

    /**
     * Attempt to send any pending invoices and credit memos to Avalara
     *
     * @return $this
     */
    protected function processItems()
    {
        $queueItemsCount = $this->config->getQueueProcessItemsLimit();
        $filters[] = $this->filterBuilder
            ->setConditionType('in')
            ->setField(Queue::STATUS)
            ->setValue([Queue::STATUS_PENDING, Queue::STATUS_RETRY])
            ->create();

        $this->searchCriteriaBuilder->addFilters($filters);
        $sortOrder = $this->sortOrderBuilder
                   ->create()
                   ->setField(Queue::QUEUE_ID)
                   ->setDirection(SortOrder::SORT_ASC);

        $items = $this->queueRepository->getList(
            $this->searchCriteriaBuilder
                ->create()
                ->setPageSize($queueItemsCount)
                ->setSortOrders([$sortOrder])
        )->getItems();

        // process items
        /** @var Queue $item */
        foreach ($items as $item) {
            $this->processQueueItem($item);
        }

        return $this;
    }

    /**
     * Process queue item
     *
     * @param Queue $queue
     */
    protected function processQueueItem(Queue $queue)
    {
        $newAttemptValue = $queue->getAttempt() + 1;
        $queue->setAttempt($newAttemptValue);
        try {
            $queueObject = $this->getQueueObject($queue);
            $tool = $this->getToolObject($queue);
            $tool->setQueueObject($queueObject)->setQueue($queue);
            if ($queueObject->getId()) {
                $tool->execute();
            }
            $queue->setStatus(Queue::STATUS_COMPLETE)->setMessage(null)->save();
        } catch (\OnePica\AvaTax\Model\Service\Exception\Unbalanced $e) {
            $queue->setStatus(Queue::STATUS_UNBALANCED)
                ->setMessage($e->getMessage())
                ->save();
        } catch (\Exception $e) {
            $status = ($queue->getAttempt() >= Queue::ATTEMPT_MAX)
                ? Queue::STATUS_FAILED
                : Queue::STATUS_RETRY;
            $queue->setStatus($status)
                ->setMessage($e->getMessage())
                ->save();
        }
    }

    /**
     * Get queue object
     *
     * @param Queue $queueItem
     *
     * @return Invoice|Creditmemo
     */
    protected function getQueueObject(Queue $queueItem)
    {
        if ($queueItem->getType() === Queue::TYPE_INVOICE) {
            return $this->invoiceRepository->get($queueItem->getEntityId());
        }

        return $this->creditmemoRepository->get($queueItem->getEntityId());
    }

    /**
     * Get tool object by queue item
     *
     * @param Queue $queue
     *
     * @return CreditmemoServiceTool|InvoiceServiceTool
     */
    protected function getToolObject(Queue $queue)
    {
        if ($queue->getType() === Queue::TYPE_INVOICE) {
            return $this->invoiceServiceTool;
        }

        return $this->creditmemoServiceTool;
    }
}