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
use Magento\Framework\Stdlib\DateTime;
use OnePica\AvaTax\Api\QueueRepositoryInterface;
use OnePica\AvaTax\Model\Queue;
use OnePica\AvaTax\Helper\Config;

/**
 * Class Processor
 *
 * @package OnePica\AvaTax\Model\Queue
 */

class Processor
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
     * Constructor.
     *
     * @param QueueRepositoryInterface $queueRepository
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder,
     * @param FilterBuilder            $filterBuilder
     * @param Config                   $config
     * @param DateTime                 $dateTime
     */
    public function __construct(
        QueueRepositoryInterface $queueRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        Config $config,
        DateTime $dateTime
    ) {
        $this->queueRepository = $queueRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->config = $config;
        $this->dateTime = $dateTime;
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
     * Process queues
     *
     * @return $this
     */
    public function processQueues()
    {
        $this->cleanCompleted()
            ->cleanFailed();
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
}
