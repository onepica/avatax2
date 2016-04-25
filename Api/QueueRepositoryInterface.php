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
namespace OnePica\AvaTax\Api;

/**
 * Interface QueueRepositoryInterface
 *
 * @package OnePica\AvaTax\Api
 */
interface QueueRepositoryInterface
{
    /**
     * Save Queue
     *
     * @param \OnePica\AvaTax\Api\Data\QueueInterface $queue
     * @return \OnePica\AvaTax\Api\Data\QueueInterface
     */
    public function save(Data\QueueInterface $queue);

    /**
     * Delete Queue
     *
     * @param \OnePica\AvaTax\Api\Data\QueueInterface $queue
     * @return bool
     */
    public function delete(Data\QueueInterface $queue);

    /**
     * Retrieve Queue
     *
     * @param int $queueId
     * @return \OnePica\AvaTax\Api\Data\QueueInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($queueId);

    /**
     * Load Queue data collection by given search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magento\Cms\Model\ResourceModel\Page\Collection
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria);

    /**
     * Get queue count by criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return int
     */
    public function getCountByCriteria(\Magento\Framework\Api\SearchCriteriaInterface $criteria);
}
