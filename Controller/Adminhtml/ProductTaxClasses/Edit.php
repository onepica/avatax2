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
 * @author     Astound Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Astound\AvaTax\Controller\Adminhtml\ProductTaxClasses;


use Astound\AvaTax\Controller\Adminhtml\TaxClass\AbstractEditAction;
use Magento\Tax\Model\ClassModel;
use Magento\Backend\Model\View\Result\Page;

/**
 * Class Edit
 *
 * @package Astound\AvaTax\Controller\Adminhtml\ProductTaxClasses
 */
class Edit extends AbstractEditAction
{
    /**
     * Init Page
     *
     * @param Page       $resultPage
     * @param ClassModel $model
     *
     * @return $this
     */
    protected function _initPage(Page $resultPage, ClassModel $model)
    {
        $resultPage->setActiveMenu('Astound_AvaTax::product_tax_classes');

        $resultPage->getConfig()->getTitle()->prepend(__('Tax Classes'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getClassName() : __('New Tax Class'));

        return $this;
    }

    /**
     * Validate model and redirect if validation fails
     *
     * @param ClassModel $model
     *
     * @return $this|null
     */
    protected function _validateAndRedirect(ClassModel $model)
    {
        if ($model->getClassType() == 'CUSTOMER') {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath("*/customertaxclasses/edit/id/{$model->getId()}/");
        }

        return null;
    }

    /**
     * Access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Astound_AvaTax::product_tax_classes');
    }
}
