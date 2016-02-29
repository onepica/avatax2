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
namespace OnePica\AvaTax16\Document\Response\Line\CalculatedTax;

use OnePica\AvaTax16\Document\Part;

/**
 * Class \OnePica\AvaTax16\Document\Response\Line\CalculatedTax\Details
 *
 * @method string getJurisdictionName()
 * @method setJurisdictionName(string $value)
 * @method string getJurisdictionType()
 * @method setJurisdictionType(string $value)
 * @method string getTaxType()
 * @method setTaxType(string $value)
 * @method string getRateType()
 * @method setRateType(string $value)
 * @method string getScenario()
 * @method setScenario(string $value)
 * @method float getSubtotalTaxable()
 * @method setSubtotalTaxable(float $value)
 * @method float getSubtotalExempt()
 * @method setSubtotalExempt(float $value)
 * @method float getRate()
 * @method setRate(float $value)
 * @method float getTax()
 * @method setTax(float $value)
 * @method bool getExempt()
 * @method setExempt(bool $value)
 * @method string getExemptionReason()
 * @method setExemptionReason(string $value)
 * @method array getSignificantLocations()
 * @method setSignificantLocations(array $value)
 * @method string getComment()
 * @method setComment(string $value)
 */
class Details extends Part
{
    /**
     * Jurisdiction Name
     *
     * @var string
     */
    protected $_jurisdictionName;

    /**
     * Jurisdiction Type
     *
     * @var string
     */
    protected $_jurisdictionType;

    /**
     * Tax Type
     *
     * @var string
     */
    protected $_taxType;

    /**
     * Rate Type
     *
     * @var string
     */
    protected $_rateType;

    /**
     * Scenario
     *
     * @var string
     */
    protected $_scenario;

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
     * Rate
     *
     * @var float
     */
    protected $_rate;

    /**
     * Tax
     *
     * @var float
     */
    protected $_tax;

    /**
     * Exempt
     *
     * @var bool
     */
    protected $_exempt;

    /**
     * ExemptionReason
     *
     * @var string
     */
    protected $_exemptionReason;

    /**
     * Significant Locations
     *
     * @var string[]
     */
    protected $_significantLocations;

    /**
     * Comment
     *
     * @var string
     */
    protected $_comment;
}
