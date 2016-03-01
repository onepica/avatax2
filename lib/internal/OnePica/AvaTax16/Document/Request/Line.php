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
namespace OnePica\AvaTax16\Document\Request;

use OnePica\AvaTax16\Document\Part;

/**
 * Class \OnePica\AvaTax16\Document\Request\Line
 *
 * @method string getLineCode()
 * @method setLineCode(string $value)
 * @method string getItemCode()
 * @method setItemCode(string $value)
 * @method string getAvalaraGoodsAndServicesType()
 * @method setAvalaraGoodsAndServicesType(string $value)
 * @method string getAvalaraGoodsAndServicesModifierType()
 * @method setAvalaraGoodsAndServicesModifierType(string $value)
 * @method float getNumberOfItems()
 * @method setNumberOfItems(float $value)
 * @method float getLineAmount()
 * @method setLineAmount(float $value)
 * @method bool getDiscounted()
 * @method setDiscounted(bool $value)
 * @method string getItemDescription()
 * @method setItemDescription(string $value)
 * @method string getUnitOfMeasure()
 * @method setUnitOfMeasure(string $value)
 * @method array getLocations()
 * @method setLocations(array $value)
 * @method string getTaxPayerCode()
 * @method setTaxPayerCode(string $value)
 * @method string getBuyerType()
 * @method setBuyerType(string $value)
 * @method string getUseType()
 * @method setUseType(string $value)
 * @method float getTaxOverrideAmount()
 * @method setTaxOverrideAmount(float $value)
 * @method bool getTaxIncluded()
 * @method setTaxIncluded(bool $value)
 * @method array getMetadata()
 */
class Line extends Part
{
    /**
     * Required properties
     *
     * @var array
     */
    protected $requiredProperties = array('lineCode', 'lineAmount');

    /**
     * Types of complex properties
     *
     * @var array
     */
    protected $propertyComplexTypes = array(
        'locations' => array(
            'type' => '\OnePica\AvaTax16\Document\Part\Location',
            'isArrayOf' => 'true'
        ),
    );

    /**
     * Line Code
     * (Required)
     *
     * @var string
     */
    protected $lineCode;

    /**
     * Item code
     *
     * @var string
     */
    protected $itemCode;

    /**
     * Avalara Goods And Services Type
     *
     * @var string
     */
    protected $avalaraGoodsAndServicesType;

    /**
     * Avalara Goods And Services Modifier Type
     *
     * @var string
     */
    protected $avalaraGoodsAndServicesModifierType;

    /**
     * Number Of Items
     *
     * @var float
     */
    protected $numberOfItems;

    /**
     * Line Amount (The total cost of this line. In its simplest form lineAmount = unit price * numberOfItems)
     * (Required)
     *
     * @var float
     */
    protected $lineAmount;

    /**
     * Discounted
     *
     * @var bool
     */
    protected $discounted;

    /**
     * Item Description
     *
     * @var string
     */
    protected $itemDescription;

    /**
     * Unit Of Measure
     * (Not currently supported)
     *
     * @var string
     */
    protected $unitOfMeasure;

    /**
     * Locations
     *
     * @var \OnePica\AvaTax16\Document\Part\Location[]
     */
    protected $locations;

    /**
     * Tax Payer Code
     * (Not currently supported)
     *
     * @var string
     */
    protected $taxPayerCode;

    /**
     * Buyer Type
     *
     * @var string
     */
    protected $buyerType;

    /**
     * Use Type
     *
     * @var string
     */
    protected $useType;

    /**
     * Tax Override Amount
     * (Not currently supported)
     *
     * @var float
     */
    protected $taxOverrideAmount;

    /**
     * Tax Included
     * (Not currently supported)
     *
     * @var bool
     */
    protected $taxIncluded;

    /**
     * Tax Included
     *
     * @var array
     */
    protected $metadata;

    /**
     * Set Metadata
     *
     * @param array|\StdClass $value
     * @return $this
     */
    public function setMetadata($value)
    {
        if ($value instanceof \StdClass) {
            // convert object data to array
            // it is used during filling data from response
            $this->metadata = (array) $value;
        } else {
            $this->metadata = $value;
        }
    }
}
