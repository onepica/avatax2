<?php
/**
 * Astound_AvaTax
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0),
 * a copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Astound
 * @package    Astound_AvaTax
 * @author     Astound Codemaster <codemaster@astoundcommerce.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Api\Payment;

/**
 * Interface PaymentDetailsExtensionInterface
 *
 * @package Astound\AvaTax\Api\Payment
 */
interface PaymentDetailsExtensionInterface extends \Magento\Checkout\Api\Data\PaymentDetailsExtensionInterface
{
    /**
     * @return \Magento\Quote\Api\Data\AddressInterface|null
     */
    public function getValidatedAddress();

    /**
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @return $this
     */
    public function setValidatedAddress(\Magento\Quote\Api\Data\AddressInterface $address);
}