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
use OnePica\AvaTax\Api\QueueRepositoryInterface;
use OnePica\AvaTax\Model\Queue;

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
     * Constructor.
     *
     * @param QueueRepositoryInterface $queueRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder,
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        QueueRepositoryInterface $queueRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->queueRepository = $queueRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
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
        return $this;
    }
}
