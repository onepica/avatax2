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

namespace OnePica\AvaTax\Controller\Adminhtml\CustomerTaxClasses;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\ForwardFactory;

class Index extends Action
{
    protected $resultPageFactory;

    protected $resultForwardFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
    }

    public function execute()
    {
        try{
            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('OnePica_AvaTax::customer_tax_classes');
            $resultPage->addBreadcrumb(__('AvaTax'), __('AvaTax'));
            $resultPage->addBreadcrumb(__('Customer Tax Classes'), __('Customer Tax Classes'));
            $resultPage->getConfig()->getTitle()->prepend(__('Customer Tax Classes'));
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $resultForward = $this->_forwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('OnePica_AvaTax::customer_tax_classes');
    }
}
