<?php
/**
 * OnePica_AvaTax
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0),
 * a copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 * @copyright  Copyright (c) 2016 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax\Model\Service\Result;

use OnePica\AvaTax\Model\Service\Request\Address;

/**
 * Class AddressValidationResult
 *
 * @package OnePica\AvaTax\Model\Service\Result
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