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
namespace OnePica\AvaTax16\Calculation;

use OnePica\AvaTax16\Document\Part;

/**
 * Class \OnePica\AvaTax16\Calculation\ListItemResponse
 *
 * @method \OnePica\AvaTax16\Calculation\ListItemResponse\Header getHeader()
 * @method setHeader(\OnePica\AvaTax16\Calculation\ListItemResponse\Header $value)
 * @method array getLines)
 * @method setLines(array $value)
 * @method \OnePica\AvaTax16\Calculation\ListItemResponse\CalculatedTaxSummary getCalculatedTaxSummary()
 * @method setCalculatedTaxSummary(\OnePica\AvaTax16\Calculation\ListItemResponse\CalculatedTaxSummary $value)
 * @method \OnePica\AvaTax16\Calculation\ListItemResponse\ProcessingInfo getProcessingInfo()
 * @method setProcessingInfo(\OnePica\AvaTax16\Calculation\ListItemResponse\ProcessingInfo $value)
 */
class ListItemResponse extends Part
{
    /**
     * Types of complex properties
     *
     * @var array
     */
    protected $_propertyComplexTypes = array(
        '_header' => array(
            'type' => '\OnePica\AvaTax16\Calculation\ListItemResponse\Header'
        ),
        '_lines' => array(
            'type' => '\OnePica\AvaTax16\Calculation\ListItemResponse\Line',
            'isArrayOf' => 'true'
        ),
        '_calculatedTaxSummary' => array(
            'type' => '\OnePica\AvaTax16\Calculation\ListItemResponse\CalculatedTaxSummary'
        ),
        '_processingInfo' => array(
            'type' => '\OnePica\AvaTax16\Calculation\ListItemResponse\ProcessingInfo'
        ),
    );

    /**
     * Header
     *
     * @var \OnePica\AvaTax16\Calculation\ListItemResponse\Header
     */
    protected $_header;

    /**
     * Lines
     *
     * @var Array
     */
    protected $_lines;

    /**
     * Feedback
     *
     * @var \OnePica\AvaTax16\Calculation\ListItemResponse\CalculatedTaxSummary
     */
    protected $_calculatedTaxSummary;

    /**
     * Feedback
     *
     * @var \OnePica\AvaTax16\Calculation\ListItemResponse\ProcessingInfo
     */
    protected $_processingInfo;
}
