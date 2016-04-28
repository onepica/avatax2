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
 * @author     OnePica Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax\Cron;

use OnePica\AvaTax\Api\QueueManagementInterface;

/**
 * Class ScheduledQueueProcessing
 *
 * @package OnePica\AvaTax\Cron
 */
class ScheduledQueueProcessing
{
    /**
     * Queue management
     *
     * @var QueueManagementInterface
     */
    protected $queueManagement;

    /**
     * ScheduledQueueProcessing constructor.
     *
     * @param QueueManagementInterface $queueManagement
     */
    public function __construct(QueueManagementInterface $queueManagement)
    {
        $this->queueManagement = $queueManagement;
    }

    /**
     * Process queues
     */
    public function execute()
    {
        $this->queueManagement->processQueue();
    }
}
