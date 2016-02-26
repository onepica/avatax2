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
namespace OnePica\AvaTax16\Document\Response\CalculatedTaxSummary\TaxByType;

use OnePica\AvaTax16\Document\Part;

/**
 * Class \OnePica\AvaTax16\Document\Response\CalculatedTaxSummary\TaxByType\Details
 *
 * @method string getJurisdictionName()
 * @method setJurisdictionName(string $value)
 * @method string getJurisdictionType()
 * @method setJurisdictionType(string $value)
 * @method float getTax()
 * @method setTax(float $value)
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
     * Tax
     *
     * @var float
     */
    protected $_tax;

    /**
     * Comment
     *
     * @var string
     */
    protected $_comment;
}
