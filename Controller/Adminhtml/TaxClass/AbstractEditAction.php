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

namespace OnePica\AvaTax\Controller\Adminhtml\TaxClass;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Tax\Api\TaxClassRepositoryInterface;
use Magento\Tax\Model\ClassModelFactory;
use Magento\Tax\Model\ClassModel;

use \Magento\Framework\Registry;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class AbstractEditAction
 *
 * @package OnePica\AvaTax\Controller\Adminhtml\CustomerTaxClasses
 */
abstract class AbstractEditAction extends Action
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
     * @return $this|\Magento\Backend\Model\View\Result\Page|\Magento\Framework\Controller\Result\Forward
     */
    public function execute()
    {
        try {
            $id = $this->getRequest()->getParam('id');
            $model = (!isset($id)) ? $this->factoryTaxClass->create()
                : $this->repositoryTaxClass->get($id);

            $result = $this->_validateAndRedirect($model);
            if (isset($result)) {
                return $result;
            }

            $data = $this->_objectManager->get(\Magento\Backend\Model\Session::class)
                ->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            $this->coreRegistry->register('tax_class', $model);

            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $this->_initPage($resultPage, $model);

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
     * Init Page
     *
     * @param Page       $resultPage
     * @param ClassModel $model
     *
     * @return mixed
     */
    protected abstract function _initPage(Page $resultPage, ClassModel $model);

    /**
     * Validate model and redirect if validation fails
     *
     * @param ClassModel $model
     *
     * @return mixed
     */
    protected abstract function _validateAndRedirect(ClassModel $model);
}
