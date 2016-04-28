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

namespace OnePica\AvaTax\Controller\Adminhtml\ProductTaxClasses;

use OnePica\AvaTax\Controller\Adminhtml\TaxClass\AbstractIndexAction;

use Magento\Backend\Model\View\Result\Page;

/**
 * Class Index
 *
 * @package OnePica\AvaTax\Controller\Adminhtml\ProductTaxClasses
 */
class Index extends AbstractIndexAction
{

    /**
     * Init Page
     *
     * @param Page $resultPage
     *
     * @return $this
     */
    protected function _initPage(Page $resultPage)
    {
        $resultPage->setActiveMenu('OnePica_AvaTax::product_tax_classes');
        $resultPage->addBreadcrumb(__('AvaTax'), __('AvaTax'));
        $resultPage->addBreadcrumb(__('Product Tax Classes'), __('Product Tax Classes'));
        $resultPage->getConfig()->getTitle()->prepend(__('Product Tax Classes'));

        return $this;
    }

    /**
     * Access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('OnePica_AvaTax::product_tax_classes');
    }
}
