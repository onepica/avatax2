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
namespace OnePica\AvaTax16\AddressResolution;

use OnePica\AvaTax16\Document\Part;

/**
 * Class \OnePica\AvaTax16\AddressResolution\ResolveSingleAddressResponse
 *
 * @method bool getHasError()
 * @method setHasError(bool $value)
 * @method array getErrors()
 * @method setErrors(array $value)
 * @method \OnePica\AvaTax16\Document\Part\Location\Address getAddress()
 * @method setAddress(\OnePica\AvaTax16\Document\Part\Location\Address $value)
 * @method \OnePica\AvaTax16\Document\Part\Location\LatLong getCoordinates()
 * @method setCoordinates(\OnePica\AvaTax16\Document\Part\Location\LatLong $value)
 * @method string getResolutionQuality()
 * @method setResolutionQuality(string $value)
 * @method array getTaxAuthorities()
 * @method setTaxAuthorities(array $value)
 */
class ResolveSingleAddressResponse extends Part
{
    /**
     * Has error
     *
     * @var bool
     */
    protected $hasError = false;

    /**
     * Errors
     *
     * @var array
     */
    protected $errors;

    /**
     * Types of complex properties
     *
     * @var array
     */
    protected $propertyComplexTypes = array(
        'address' => array(
            'type' => '\OnePica\AvaTax16\Document\Part\Location\Address'
        ),
        'coordinates' => array(
            'type' => '\OnePica\AvaTax16\Document\Part\Location\LatLong'
        ),
        'taxAuthorities' => array(
            'type' => '\OnePica\AvaTax16\AddressResolution\TaxAuthority',
            'isArrayOf' => 'true'
        ),
    );

    /**
     * Address
     *
     * @var \OnePica\AvaTax16\Document\Part\Location\Address
     */
    protected $address;

    /**
     * Coordinates
     *
     * @var \OnePica\AvaTax16\Document\Part\Location\LatLong
     */
    protected $coordinates;

    /**
     * Resolution Quality
     *
     * @var string
     */
    protected $resolutionQuality;

    /**
     * Tax Authorities
     *
     * @var array
     */
    protected $taxAuthorities;
}
