<?php
/**
 * OnePica_AvaTax
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0),
 * a copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax\Model\Service\Request;

use Magento\Store\Model\Store;

/**
 * Class Address
 *
 * @package OnePica\AvaTax\Model\Service\Request
 */
class Address
{
    /**
     * Store
     *
     * @var Store
     */
    protected $store;

    /**
     * Line 1
     *
     * @var string
     */
    protected $line1;

    /**
     * Line 2
     *
     * @var string
     */
    protected $line2;

    /**
     * City
     *
     * @var string
     */
    protected $city;

    /**
     * Region
     *
     * @var string
     */
    protected $region;

    /**
     * Postcode
     *
     * @var string
     */
    protected $postcode;

    /**
     * Country
     *
     * @var string
     */
    protected $country;


    /**
     * Get request
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Set store
     *
     * @param Store $store
     * @return $this
     */
    public function setStore($store)
    {
        return $this->store = $store;
    }

    /**
     * Get line 1
     *
     * @return string
     */
    public function getLine1()
    {
        return $this->line1;
    }

    /**
     * Set line 1
     *
     * @param string $value
     * @return $this
     */
    public function setLine1($value)
    {
        return $this->line1 = $value;
    }

    /**
     * Get line 2
     *
     * @return string
     */
    public function getLine2()
    {
        return $this->line2;
    }

    /**
     * Set line 2
     *
     * @param string $value
     * @return $this
     */
    public function setLine2($value)
    {
        return $this->line2 = $value;
    }

    /**
     * Get City
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set City
     *
     * @param string $value
     * @return $this
     */
    public function setCity($value)
    {
        return $this->city = $value;
    }

    /**
     * Get Region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set Region
     *
     * @param string $value
     * @return $this
     */
    public function setRegion($value)
    {
        return $this->region = $value;
    }

    /**
     * Get Postcode
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set Postcode
     *
     * @param string $value
     * @return $this
     */
    public function setPostcode($value)
    {
        return $this->postcode = $value;
    }

    /**
     * Get Country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set Country
     *
     * @param string $value
     * @return $this
     */
    public function setCountry($value)
    {
        return $this->country = $value;
    }
}
