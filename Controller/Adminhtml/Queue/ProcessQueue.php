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
namespace Astound\AvaTax\Controller\Adminhtml\Queue;

use Astound\AvaTax\Api\QueueManagementInterface;
use Astound\AvaTax\Controller\Adminhtml\AbstractQueueAction;
use Magento\Backend\App\Action\Context;

/**
 * Class ProcessQueue
 *
 * @package Astound\AvaTax\Controller\Adminhtml\Log
 */
class ProcessQueue extends AbstractQueueAction
{
    /**
     * Queue management model
     *
     * @var QueueManagementInterface
     */
    protected $queueManagement;

    /**
     * Constructor
     *
     * @param Context                  $context
     * @param QueueManagementInterface $queueManagement
     */
    public function __construct(
        Context $context,
        QueueManagementInterface $queueManagement
    ) {
        parent::__construct($context);
        $this->queueManagement = $queueManagement;
    }

    /**
     * Dispatch request
     */
    public function execute()
    {
        try {
            $this->queueManagement->processQueue();
            $this->getMessageManager()->addSuccess(__('Queue was processed successfully'));
        } catch (\Exception $e) {
            $this->getMessageManager()->addError(__('Unable to process Queue'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
