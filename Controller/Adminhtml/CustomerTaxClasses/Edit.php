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
use Magento\Tax\Api\TaxClassRepositoryInterface;
use Magento\Tax\Model\ClassModelFactory;
use \Magento\Framework\Registry;

use Magento\Framework\Exception\NoSuchEntityException;
/**
 * Class Edit
 *
 * @package OnePica\AvaTax\Controller\Adminhtml\CustomerTaxClasses
 */
class Edit extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var TaxClassRepositoryInterface
     */
    protected $repositoryTaxClass;

    /**
     * @var ClassModelFactory
     */
    protected $factoryTaxClass;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Edit constructor.
     *
     * @param Context                     $context
     * @param PageFactory                 $resultPageFactory
     * @param ForwardFactory              $resultForwardFactory
     * @param TaxClassRepositoryInterface $repositoryTaxClass
     * @param ClassModelFactory           $factoryTaxClass
     * @param Registry                    $registry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        TaxClassRepositoryInterface $repositoryTaxClass,
        ClassModelFactory $factoryTaxClass,
        Registry $registry
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->repositoryTaxClass = $repositoryTaxClass;
        $this->factoryTaxClass = $factoryTaxClass;
        $this->coreRegistry = $registry;
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('OnePica_AvaTax::customer_tax_classes')
            ->addBreadcrumb(__('AvaTax'), __('AvaTax'))
            ->addBreadcrumb(__('Customer Tax Classes'), __('Customer Tax Classes'));
        return $resultPage;
    }

    /**
     * @return $this|\Magento\Backend\Model\View\Result\Page|\Magento\Framework\Controller\Result\Forward
     */
    public function execute()
    {
        try {
            $id = $this->getRequest()->getParam('id');
            $model = (!isset($id)) ? $this->factoryTaxClass->create()
                : $this->repositoryTaxClass->get($id);

            if (isset($id) && $model->getClassType()=='PRODUCT') {
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath("*/producttaxclasses/edit/id/{$id}/");
            }

            $data = $this->_objectManager->get(\Magento\Backend\Model\Session::class)
                ->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            $this->coreRegistry->register('tax_class', $model);

            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $resultPage = $this->_initAction();
            $resultPage->addBreadcrumb(
                $id ? __('Edit Tax Class') : __('New Tax Class'),
                $id ? __('Edit Tax Class') : __('New Tax Class')
            );
            $resultPage->getConfig()->getTitle()->prepend(__('Tax Classes'));
            $resultPage->getConfig()->getTitle()
                 ->prepend($model->getId() ? $model->getTitle() : __('New Tax Class'));
        } catch(NoSuchEntityException $e) {
            $this->messageManager->addError(__('This tax class no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $this->messageManager->addError(__($e->getMessage()));
            /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('OnePica_AvaTax::customer_tax_classes');
    }
}
