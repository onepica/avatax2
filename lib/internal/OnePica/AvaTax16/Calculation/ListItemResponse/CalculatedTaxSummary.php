<?php
/**
 * OnePica
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to codemaster@onepica.com so we can send you a copy immediately.
 *
 * @category  OnePica
 * @package   OnePica_AvaTax16
 * @copyright Copyright (c) 2016 One Pica, Inc. (http://www.onepica.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax16\Calculation\ListItemResponse;

use OnePica\AvaTax16\Document\Part;

/**
 * Class \OnePica\AvaTax16\Calculation\ListItemResponse\CalculatedTaxSummary
 *
 * @method int getNumberOfLines()
 * @method setNumberOfLines(int $value)
 * @method float getSubtotal()
 * @method setSubtotal(float $value)
 * @method float getTotalTax()
 * @method setTotalTax(float $value)
 * @method \OnePica\AvaTax16\Calculation\ListItemResponse\CalculatedTaxSummary\TaxByType getTaxByType()
 * @method setTaxByType(\OnePica\AvaTax16\Calculation\ListItemResponse\CalculatedTaxSummary\TaxByType $value)
 * @method float getSubtotalTaxable()
 * @method setSubtotalTaxable(float $value)
 * @method float getSubtotalExempt()
 * @method setSubtotalExempt(float $value)
 * @method float getTax()
 * @method setTax(float $value)
 * @method float getGrandTotal()
 * @method setGrandTotal(float $value)
 */
class CalculatedTaxSummary extends Part
{
    /**
     * Types of complex properties
     *
     * @var array
     */
    protected $_propertyComplexTypes = array(
        '_taxByType' => array(
            'type' => '\OnePica\AvaTax16\Calculation\ListItemResponse\CalculatedTaxSummary\TaxByType',
            'isArrayOf' => 'true'
        )
    );

    /**
     * Number Of Lines
     *
     * @var int
     */
    protected $_numberOfLines;

    /**
     * Subtotal
     *
     * @var float
     */
    protected $_subtotal;

    /**
     * Total Tax
     *
     * @var float
     */
    protected $_totalTax;

    /**
     * Tax By Type
     *
     * @var \OnePica\AvaTax16\Calculation\ListItemResponse\CalculatedTaxSummary\TaxByType
     */
    protected $_taxByType;

    /**
     * Subtotal Taxable
     *
     * @var float
     */
    protected $_subtotalTaxable;

    /**
     * Subtotal Exempt
     *
     * @var float
     */
    protected $_subtotalExempt;

    /**
     * Tax
     *
     * @var float
     */
    protected $_tax;

    /**
     * Grand Total
     *
     * @var float
     */
    protected $_grandTotal;
}
