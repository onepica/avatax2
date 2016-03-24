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
namespace OnePica\AvaTax\Controller\Adminhtml\Queue;

use OnePica\AvaTax\Controller\Adminhtml\AbstractQueueAction;
use Magento\Backend\App\Action\Context;
use OnePica\AvaTax\Model\Queue\Processor as QueueProcessor;

/**
 * Class ClearQueue
 *
 * @package OnePica\AvaTax\Controller\Adminhtml\Log
 */
class ClearQueue extends AbstractQueueAction
{
    /**
     * Queue processor model
     *
     * @var QueueProcessor
     */
    protected $queueProcessor;

    /**
     * Constructor
     *
     * @param Context        $context
     * @param QueueProcessor $queueProcessor
     */
    public function __construct(
        Context $context,
        QueueProcessor $queueProcessor)
    {
        parent::__construct($context);
        $this->queueProcessor = $queueProcessor;
    }

    /**
     * Dispatch request
     */
    public function execute()
    {
        try {
            $this->queueProcessor->clear();
            $this->getMessageManager()->addSuccess(__('Queues were cleared successfully'));
        } catch (\Exception $e) {
            $this->getMessageManager()->addError(__('Unable to clear Queues'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
