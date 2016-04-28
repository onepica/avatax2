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
 * @author     Astound Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Plugin\Checkout\Model;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Framework\ObjectManagerInterface;
use Astound\AvaTax\Helper\Config;
use Astound\AvaTax\Helper\Address as AvataxAddressHelper;

/**
 * Class ShippingInformationManagement
 */
class ShippingInformationManagement
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Quote repository
     *
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     *  Address repository
     *
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * Address helper
     *
     * @var AvataxAddressHelper
     */
    protected $addressHelper;

    /**
     * ShippingInformationManagement constructor
     *
     * @param Config $config
     * @param CartRepositoryInterface $quoteRepository
     * @param ObjectManagerInterface $objectManager
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param AvataxAddressHelper $addressHelper
     */
    public function __construct(
        Config $config,
        CartRepositoryInterface $quoteRepository,
        ObjectManagerInterface $objectManager,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        AvataxAddressHelper $addressHelper
    ) {
        $this->config = $config;
        $this->quoteRepository = $quoteRepository;
        $this->objectManager = $objectManager;
        $this->addressRepository = $addressRepository;
        $this->addressHelper = $addressHelper;
    }

    /**
     * Save Address Information
     *
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param \Closure $proceed
     * @param $cartId,
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function aroundSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $customerAddressId = $addressInformation->getShippingAddress()->getCustomerAddressId();
        $quote = $this->quoteRepository->getActive($cartId);
        $storeId = $quote->getStoreId();

        if ($customerAddressId) {
            $addressData = $this->addressRepository->getById($customerAddressId);
            $quote->getShippingAddress()->importCustomerAddressData($addressData);
        }

        $this->validateAndNormalize($addressInformation, $storeId);
        $paymentInformation = $proceed($cartId, $addressInformation);
        // set updated addresses for response
        $paymentDetailsExtensionAttributes = $this->objectManager
            ->get('\Astound\AvaTax\Model\Payment\PaymentDetailsExtension');
        $paymentDetailsExtensionAttributes->setValidatedAddress($addressInformation->getShippingAddress());
        $paymentInformation->setExtensionAttributes($paymentDetailsExtensionAttributes);

        return $paymentInformation;
    }

    /**
     * Validate and normalize
     *
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @param int $storeId
     */
    protected function validateAndNormalize(
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation,
        $storeId
    ) {
        $shippingAddress = $addressInformation->getShippingAddress();
        $shippingAddress->setAddressType(Address::ADDRESS_TYPE_SHIPPING);
        $validationResult = $shippingAddress->validate();

        if ($validationResult !== true) {
            $this->showErrorsAndStopCheckout($validationResult);
        } elseif ($shippingAddress->getData('is_normalized')) {
            // add normalize message
            $message = $this->config->getOnepageNormalizeMessage($storeId);
            $this->addressHelper->addValidationNotice($message);

            /** fix for logged in users with address
             * due to address changes it will be new address
             */
            $shippingAddress->setCustomerAddressId(null);
        }
    }

    /**
     * Show errors and stop checkout
     *
     * @param array $errors
     * @throws \Exception
     */
    protected function showErrorsAndStopCheckout(array $errors)
    {
        throw new \Magento\Framework\Webapi\Exception(new \Magento\Framework\Phrase(implode(', ', $errors)));
    }
}
