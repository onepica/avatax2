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
 * @package   OnePica_AvaTax
 * @copyright Copyright (c) 2015 One Pica, Inc. (http://www.onepica.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax16\Document\Response\CalculatedTaxSummary;

use OnePica\AvaTax16\Document\Part;

/**
 * Class \OnePica\AvaTax16\Document\Response\CalculatedTaxSummary\TaxByType
 *
 * @method float getTax()
 * @method setTax(float $value)
 * @method array getJurisdictions()
 * @method setJurisdictions(array $value)
 * @method string getComment()
 * @method setComment(string $value)
 */
class TaxByType extends Part
{
    /**
     * Types of complex properties
     *
     * @var array
     */
    protected $_propertyComplexTypes = array(
        '_jurisdictions' => array(
            'type' => '\OnePica\AvaTax16\Document\Response\CalculatedTaxSummary\TaxByType\Details',
            'isArrayOf' => true
        )
    );

    /**
     * Tax
     *
     * @var float
     */
    protected $_tax;

    /**
     * jurisdictions
     *
     * @var \OnePica\AvaTax16\Document\Response\CalculatedTaxSummary\TaxByType\Details[]
     */
    protected $_jurisdictions;

    /**
     * Comment
     *
     * @var string
     */
    protected $_comment;
}
