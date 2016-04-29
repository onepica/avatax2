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
namespace Astound\AvaTax\Model;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Stdlib\DateTime;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Astound\AvaTax\Api\QueueManagementInterface;
use Astound\AvaTax\Api\QueueRepositoryInterface;
use Astound\AvaTax\Helper\Config;
use Astound\AvaTax\Model\Tool\Submit;

/**
 * Class Manager
 *
 * @package Astound\AvaTax\Model\Queue
 */
class QueueManagement implements QueueManagementInterface
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
     * @var \Astound\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * DateTime model
     *
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Submit tool
     *
     * @var Submit
     */
    protected $submitTool;

    /**
     * Constructor.
     *
     * @param QueueRepositoryInterface $queueRepository
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder
     * @param FilterBuilder            $filterBuilder
     * @param Config                   $config
     * @param DateTime                 $dateTime
     * @param SortOrderBuilder         $sortOrderBuilder
     * @param Submit                   $submitToll
     */
    public function __construct(
        QueueRepositoryInterface $queueRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        Config $config,
        DateTime $dateTime,
        SortOrderBuilder $sortOrderBuilder,
        Submit $submitToll
    ) {
        $this->queueRepository = $queueRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->config = $config;
        $this->dateTime = $dateTime;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->submitTool = $submitToll;
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
            $tool = $this->submitTool;
            $tool->setQueue($queue);
            $tool->execute();
            $queue->setStatus(Queue::STATUS_COMPLETE)->setMessage(null)->save();
        } catch (\Astound\AvaTax\Model\Service\Exception\Unbalanced $e) {
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
}
