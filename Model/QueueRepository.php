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
namespace OnePica\AvaTax\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SortOrder;
use OnePica\AvaTax\Api\Data;
use OnePica\AvaTax\Api\QueueRepositoryInterface;
use OnePica\AvaTax\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;

/**
 * Class QueueRepository
 *
 * @package OnePica\AvaTax\Model
 */
class QueueRepository implements QueueRepositoryInterface
{
    /**
     * Queue factory
     *
     * @var \OnePica\AvaTax\Model\QueueFactory
     */
    protected $queueFactory;

    /**
     * Queue resource model
     *
     * @var \OnePica\AvaTax\Model\ResourceModel\Queue
     */
    protected $resourceModel;

    /**
     * @var QueueCollectionFactory
     */
    protected $queueCollectionFactory;

    /**
     * @var Data\QueueSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var Data\QueueInterfaceFactory
     */
    protected $dataQueueFactory;

    /**
     * QueueRepository constructor.
     *
     * @param \OnePica\AvaTax\Model\QueueFactory        $queueFactory
     * @param \OnePica\AvaTax\Model\ResourceModel\Queue $queueResource
     * @param QueueCollectionFactory $queueCollectionFactory,
     * @param Data\QueueSearchResultsInterfaceFactory $searchResultsFactory
     * @param Data\QueueInterfaceFactory $dataQueueFactory
     */
    public function __construct(
        QueueFactory $queueFactory,
        ResourceModel\Queue $queueResource,
        QueueCollectionFactory $queueCollectionFactory,
        Data\QueueSearchResultsInterfaceFactory $searchResultsFactory,
        Data\QueueInterfaceFactory $dataQueueFactory
    ) {
        $this->queueFactory = $queueFactory;
        $this->resourceModel = $queueResource;
        $this->queueCollectionFactory = $queueCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataQueueFactory = $dataQueueFactory;
    }

    /**
     * Save queue
     *
     * @param \OnePica\AvaTax\Api\Data\QueueInterface $queue
     * @return \OnePica\AvaTax\Api\Data\QueueInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(Data\QueueInterface $queue)
    {
        try {
            $this->resourceModel->save($queue);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $queue;
    }

    /**
     * Delete queue
     *
     * @param \OnePica\AvaTax\Api\Data\QueueInterface $queue
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function delete(Data\QueueInterface $queue)
    {
        try {
            $this->resourceModel->delete($queue);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('Unable to remove queue %1', $queue->getQueueId())
            );
        }
        return true;
    }

    /**
     * Retrieve queue.
     *
     * @param int $queueId
     * @return \OnePica\AvaTax\Api\Data\QueueInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($queueId)
    {
        $queue = $this->queueFactory->create();
        $this->resourceModel->load($queue, $queueId);
        if (!$queue->getQueueId()) {
            throw new NoSuchEntityException(__('Avatax Queue with id "%1" does not exist.', $queueId));
        }

        return $queue;
    }

    /**
     * Load Queue data collection by given search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magento\Cms\Model\ResourceModel\Page\Collection
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->queueCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * Get queue count by criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return int
     */
    public function getCountByCriteria(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $collection = $this->queueCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        return $collection->getSize();
    }
}
