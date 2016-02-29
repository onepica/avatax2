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
namespace OnePica\AvaTax16\Document;

/**
 * Class \OnePica\AvaTax16\Document\Response
 *
 * @method bool getHasError()
 * @method setHasError(bool $value)
 * @method array getErrors()
 * @method setErrors(array $value)
 * @method \OnePica\AvaTax16\Document\Response\Header getHeader()
 * @method setHeader(\OnePica\AvaTax16\Document\Response\Header $value)
 * @method array getLines()
 * @method setLines(array $value)
 * @method \OnePica\AvaTax16\Document\Response\CalculatedTaxSummary getCalculatedTaxSummary()
 * @method setCalculatedTaxSummary(\OnePica\AvaTax16\Document\Response\CalculatedTaxSummary $value)
 * @method \OnePica\AvaTax16\Document\Part\Feedback getFeedback()
 * @method setFeedback(\OnePica\AvaTax16\Document\Part\Feedback $value)
 * @method \OnePica\AvaTax16\Document\Response\ProcessingInfo getProcessingInfo()
 * @method setProcessingInfo(\OnePica\AvaTax16\Document\Response\ProcessingInfo $value)
 */
class Response extends Part
{
    /**
     * Has error
     *
     * @var bool
     */
    protected $_hasError = false;

    /**
     * Errors
     *
     * @var array
     */
    protected $_errors;

    /**
     * Types of complex properties
     *
     * @var array
     */
    protected $_propertyComplexTypes = array(
        '_header' => array(
            'type' => '\OnePica\AvaTax16\Document\Response\Header'
        ),
        '_lines' => array(
            'type' => '\OnePica\AvaTax16\Document\Response\Line',
            'isArrayOf' => 'true'
        ),
        '_calculatedTaxSummary' => array(
            'type' => '\OnePica\AvaTax16\Document\Response\CalculatedTaxSummary'
        ),
        '_feedback' => array(
            'type' => '\OnePica\AvaTax16\Document\Part\Feedback'
        ),
        '_processingInfo' => array(
            'type' => '\OnePica\AvaTax16\Document\Response\ProcessingInfo'
        ),
    );

    /**
     * Header
     *
     * @var \OnePica\AvaTax16\Document\Response\Header
     */
    protected $_header;

    /**
     * Lines
     *
     * @var \OnePica\AvaTax16\Document\Response\Line[]
     */
    protected $_lines;

    /**
     * Calculated Tax Summary
     *
     * @var \OnePica\AvaTax16\Document\Response\CalculatedTaxSummary
     */
    protected $_calculatedTaxSummary;

    /**
     * Feedback
     *
     * @var \OnePica\AvaTax16\Document\Part\Feedback
     */
    protected $_feedback;

    /**
     * Processing Info
     *
     * @var \OnePica\AvaTax16\Document\Response\ProcessingInfo
     */
    protected $_processingInfo;
}
