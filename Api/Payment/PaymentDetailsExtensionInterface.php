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
 * @author     OnePica Codemaster <codemaster@onepica.com>
 * @copyright  Copyright (c) 2016 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax\Api\Payment;

/**
 * Interface PaymentDetailsExtensionInterface
 *
 * @package OnePica\AvaTax\Api\Payment
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
    public function setValidatedAddress($address);
}