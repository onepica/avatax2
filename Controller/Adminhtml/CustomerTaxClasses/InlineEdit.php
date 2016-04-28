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

namespace Astound\AvaTax\Controller\Adminhtml\CustomerTaxClasses;

use Astound\AvaTax\Controller\Adminhtml\TaxClass\AbstractInlineEditAction;

/**
 * Class InlineEdit
 *
 * @package Astound\AvaTax\Controller\Adminhtml\CustomerTaxClasses
 */
class InlineEdit extends AbstractInlineEditAction
{
    /**
     *  Access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Customer::customer_tax_classes');
    }
}
