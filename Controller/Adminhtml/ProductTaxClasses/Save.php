<?php
/**
 * Created by PhpStorm.
 * User: O.Marynych
 * Date: 2016-03-07
 * Time: 2:35 PM
 */

namespace OnePica\AvaTax\Controller\Adminhtml\ProductTaxClasses;

use OnePica\AvaTax\Controller\Adminhtml\TaxClass\AbstractSaveAction;

/**
 * Class Save
 *
 * @package OnePica\AvaTax\Controller\Adminhtml\ProductTaxClasses
 */
class Save extends AbstractSaveAction
{
    /**
     * Get Tax Class Type
     *
     * @return string
     */
    protected function _getClassType()
    {
        return 'PRODUCT';
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
