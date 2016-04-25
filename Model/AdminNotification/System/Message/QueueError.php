<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace OnePica\AvaTax\Model\AdminNotification\System\Message;

use Magento\Store\Model\Store;
use Magento\Framework\UrlInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Model\Queue;
use OnePica\AvaTax\Api\QueueRepositoryInterface;

class QueueError implements \Magento\Framework\Notification\MessageInterface
{
    /**
     * Message id
     */
    const MESSAGE_ID = 'avatax_queue_error_message_id';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    /**
     * Queue repository
     *
     * @var QueueRepositoryInterface
     */
    protected $queueRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * Queue count with status Retry pending
     *
     * @var int
     */
    protected $queueRetryPendingCount;

    /**
     * Constructor
     *
     * @param Config       $config
     * @param UrlInterface $urlBuilder
     * @param AuthorizationInterface $authorization
     * @param QueueRepositoryInterface $queueRepository
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder,
     * @param FilterBuilder            $filterBuilder
     */
    public function __construct(
        Config $config,
        UrlInterface $urlBuilder,
        AuthorizationInterface $authorization,
        QueueRepositoryInterface $queueRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
        $this->authorization = $authorization;
        $this->queueRepository = $queueRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return md5(self::MESSAGE_ID);
    }

    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->config->getErrorNotificationToolbar() && $this->getQueuePendingRetryCount();
    }

    /**
     * Count the number of pending_retry items in queue
     *
     * @return int
     */
    protected function getQueuePendingRetryCount()
    {
        if (!$this->queueRetryPendingCount) {
            $filters[] = $this->filterBuilder
                ->setConditionType('eq')
                ->setField(Queue::STATUS)
                ->setValue([Queue::STATUS_RETRY])
                ->create();

            $this->searchCriteriaBuilder->addFilters($filters);
            $this->queueRetryPendingCount = $this->queueRepository->getCountByCriteria(
                $this->searchCriteriaBuilder->create()
            );
        }
        return $this->queueRetryPendingCount;
    }

    /**
     * Retrieve message text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getText()
    {
        $count = $this->getQueuePendingRetryCount();
        $maxAttempt = $this->getQueueAttemptMaxValue();
        $message = sprintf('<strong>%s</strong>',  __('AvaTax:')) . ' ';

        if ($count == 1) {
            $message .= __('There is <strong>%1</strong> entry in the AvaTax Order Sync Queue that has errored.'
                     . ' Syncing is attemped %2 times before permanently failing.', $count, $maxAttempt);
        } else {
            $message .= __('There are <strong>%1</strong> entries in the AvaTax Order Sync Queue that have errored.'
                     . ' Syncing is attemped %2 times before permanently failing.', $count, $maxAttempt);
        }

        if ($this->isQueueGridAllowed()) {
            $message .= ' ' . __('Go to the <a href="%1">AvaTax Order Sync Queue</a>', $this->getQueueGridUrl());
        }

        return $message;
    }

    /**
     * Get Queue attempt max value
     *
     * @return int
     */
    protected function getQueueAttemptMaxValue()
    {
        return Queue::ATTEMPT_MAX;
    }

    /**
     * Is queue greed allowed
     *
     * @return bool
     */
    protected function isQueueGridAllowed()
    {
        return $this->authorization->isAllowed('OnePica_AvaTax::avatax_queue');
    }

    /**
     * Get queue grid url
     *
     * @return string|null
     */
    protected function getQueueGridUrl()
    {
        return $this->urlBuilder->getUrl('avatax/queue');
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return self::SEVERITY_CRITICAL;
    }
}
