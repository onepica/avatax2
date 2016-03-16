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
namespace OnePica\AvaTax\Controller\Adminhtml\Log;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use OnePica\AvaTax\Api\LogRepositoryInterface;
use OnePica\AvaTax\Block\Adminhtml\Log\View;
use OnePica\AvaTax\Controller\Adminhtml\AbstractLogAction;
use OnePica\AvaTax\Model\Log;

/**
 * Class Edit
 *
 * @package OnePica\AvaTax\Controller\Adminhtml\Log
 */
class Edit extends AbstractLogAction
{
    /**
     * Result page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Registry
     *
     * @var Registry
     */
    protected $registry;

    /**
     * Log repository
     *
     * @var \OnePica\AvaTax\Api\LogRepositoryInterface
     */
    protected $logRepository;

    /**
     * Index constructor.
     *
     * @param \Magento\Backend\App\Action\Context        $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Registry                                   $registry
     * @param \OnePica\AvaTax\Api\LogRepositoryInterface $logRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        LogRepositoryInterface $logRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->logRepository = $logRepository;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        try {
            /** @var Log $model */
            $model = $this->logRepository->getById($id);
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        $this->registry->register(View::REGISTRY_MODEL_KEY, $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('AvaTax Action Log Entry № %1', $model->getId()));

        return $resultPage;
    }
}
