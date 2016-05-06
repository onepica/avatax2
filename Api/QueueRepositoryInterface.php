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
namespace Astound\AvaTax\Api;

/**
 * Interface QueueRepositoryInterface
 *
 * @package Astound\AvaTax\Api
 */
interface QueueRepositoryInterface
{
    /**
     * Save Queue
     *
     * @param \Astound\AvaTax\Api\Data\QueueInterface $queue
     * @return \Astound\AvaTax\Api\Data\QueueInterface
     */
    public function save(Data\QueueInterface $queue);

    /**
     * Delete Queue
     *
     * @param \Astound\AvaTax\Api\Data\QueueInterface $queue
     * @return bool
     */
    public function delete(Data\QueueInterface $queue);

    /**
     * Retrieve Queue
     *
     * @param int $queueId
     * @return \Astound\AvaTax\Api\Data\QueueInterface
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
