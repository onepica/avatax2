<?php
/**
 * Astound_AvaTax
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0),
 * a copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Astound
 * @package    Astound_AvaTax
 * @author     Astound Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Model\Service\Result;

use Astound\AvaTax\Model\Service\Request\Address;

/**
 * Class AddressValidationResult
 *
 * @package Astound\AvaTax\Model\Service\Result
 */
interface AddressValidationResultInterface
{
    /**
     * Get address
     *
     * @return Address
     */
    public function getAddress();

    /**
     * Set address
     *
     * @param Address $address
     * @return $this
     */
    public function setAddress($address);

    /**
     * Get resolution
     *
     * @return bool
     */
    public function getResolution();

    /**
     * Set resolution
     *
     * @param bool $resolution
     * @return $this
     */
    public function setResolution($resolution);
}
