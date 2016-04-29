<?php
/**
 * Created by PhpStorm.
 * User: O.Marynych
 * Date: 2016-03-07
 * Time: 2:35 PM
 */

namespace Astound\AvaTax\Controller\Adminhtml\CustomerTaxClasses;

use Astound\AvaTax\Controller\Adminhtml\TaxClass\AbstractSaveAction;

/**
 * Class Save
 *
 * @package Astound\AvaTax\Controller\Adminhtml\CustomerTaxClasses
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
        return 'CUSTOMER';
    }

    /**
     * Access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Astound_AvaTax::customer_tax_classes');
    }
}
