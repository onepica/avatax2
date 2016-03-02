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
namespace OnePica\AvaTax16\Document\Part;

use OnePica\AvaTax16\Document\Part;

/**
 * Class \OnePica\AvaTax16\Document\Part\Location
 *
 * @method string getTaxLocationPurpose()
 * @method setTaxLocationPurpose(string $value)
 * @method \OnePica\AvaTax16\Document\Part\Location\LatLong getLatlong()
 * @method setLatlong(\OnePica\AvaTax16\Document\Part\Location\LatLong $value)
 * @method string getLocationCode()
 * @method setLocationCode(string $value)
 * @method string getIpAddress()
 * @method setIpAddress(string $value)
 * @method string getResolutionQuality()
 * @method setResolutionQuality(string $value)
 * @method string getAddressTaxPayerCode()
 * @method setAddressTaxPayerCode(string $value)
 * @method string getAddressBuyerType()
 * @method setAddressBuyerType(string $value)
 */
class Location extends Part
{
    /**
     * Types of complex properties
     *
     * @var array
     */
    protected $propertyComplexTypes = array(
        'address' => array(
            'type' => '\OnePica\AvaTax16\Document\Part\Location\Address'
        ),
        'latlong' => array(
            'type' => '\OnePica\AvaTax16\Document\Part\Location\LatLong'
        ),
        'feedback' => array(
            'type' => '\OnePica\AvaTax16\Document\Part\Feedback'
        ),
    );

    /**
     * Tax Location Purpose
     * (Required)
     *
     * @var string
     */
    protected $taxLocationPurpose;
    /**
     * Address
     *
     * @var \OnePica\AvaTax16\Document\Part\Location\Address
     */
    protected $address;

    /**
     * Latitude and longitude
     *
     * @var \OnePica\AvaTax16\Document\Part\Location\LatLong
     */
    protected $latlong;

    /**
     * Location code
     * (Not currently supported)
     *
     * @var string
     */
    protected $locationCode;

    /**
     * Ip Address
     * (Not currently supported)
     *
     * @var string
     */
    protected $ipAddress;

    /**
     * Resolution Quality
     *
     * @var string
     */
    protected $resolutionQuality;

    /**
     * Address Tax Payer Code
     * (Not currently supported)
     *
     * @var string
     */
    protected $addressTaxPayerCode;

    /**
     * Address Buyer Type
     *
     * @var string
     */
    protected $addressBuyerType;

    /**
     * Address Use Type
     *
     * @var string
     */
    protected $addressUseType;

    /**
     * Set Address
     *
     * @param \OnePica\AvaTax16\Document\Part\Location\Address $value
     * @return $this
     */
    public function setAddress($value)
    {
        $this->address = $value;
        return $this;
    }

    /**
     * Get Address
     *
     * @return \OnePica\AvaTax16\Document\Part\Location\Address
     */
    public function getAddress()
    {
        return $this->address;
    }
}
