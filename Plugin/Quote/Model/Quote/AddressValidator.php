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
namespace OnePica\AvaTax\Plugin\Quote\Model\Quote;

use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\ObjectManagerInterface;
use \Magento\Store\Model\StoreManagerInterface;
use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Model\Tool\Validate;
use OnePica\AvaTax\Model\Service\Request\Address as RequestAddress;
use OnePica\AvaTax\Model\Service\Result\AddressValidationResult;

/**
 * Quote address validator
 *
 * @package OnePica\AvaTax\Plugin\Quote\Model\Quote\AddressValidator
 */
class AddressValidator
{
    /**
     * @var Config
     */
    protected $config = null;

    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * ShippingInformationManagement constructor
     *
     * @param Config $config
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
    }

    /**
     * After validate
     *
     * @param \Magento\Customer\Model\Address\AbstractAddress $subject
     * @param array|bool $result
     * @return array|bool
     */
    public function afterValidate(AbstractAddress $subject, $result)
    {
        if ($result !== true) {
            return $result;
        }
        return $this->validate($subject);
    }

    /**
     * Validate customer address
     *
     * @param AbstractAddress $address
     * @return array|bool
     */
    public function validate(AbstractAddress $address)
    {
        if (!$this->isValidationRequired($address)) {
            return true;
        }
        $requestAddress = $this->convertAddressToRequestAddress($address);
        $validator = $this->objectManager->create(Validate::class, ['object' => $requestAddress]);
        $serviceResult = $validator->execute();
        $this->normalizeAddress($address, $serviceResult);
        $result = $serviceResult->getErrors() ? $serviceResult->getErrors() : true;

        return $result;
    }

    /**
     * Is validation required
     *
     * @param AbstractAddress $address
     * @return bool
     */
    protected function isValidationRequired(AbstractAddress $address)
    {
        if ($this->getValidationMode() &&
            $address->getAddressType() == \Magento\Quote\Model\Quote\Address::ADDRESS_TYPE_SHIPPING
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get Validation Mode
     *
     * @return int
     */
    protected function getValidationMode()
    {
        return $this->config->getValidateAddress($this->storeManager->getStore());
    }

    /**
     * Convert Address To Request Address
     *
     * @param AbstractAddress $address
     * @return RequestAddress
     */
    protected function convertAddressToRequestAddress(AbstractAddress $address)
    {
        $requestAddress = $this->objectManager->create('OnePica\AvaTax\Model\Service\Request\Address');
        $store = $this->storeManager->getStore();
        $requestAddress->setStore($store);
        $requestAddress->setLine1($address->getStreetLine(1));
        $requestAddress->setLine2($address->getStreetLine(2));
        $requestAddress->setCity($address->getCity());
        $requestAddress->setRegion($address->getRegion());
        $requestAddress->setPostcode($address->getPostcode());
        $requestAddress->setCountry($address->getCountry());

        return $requestAddress;
    }

    /**
     * Normalize Address
     *
     * @param AbstractAddress $address
     * @param AddressValidationResult $serviceResult
     */
    protected function normalizeAddress(AbstractAddress $address, AddressValidationResult $serviceResult)
    {
        if ($this->isNormalizeAddressEnabled() && !$serviceResult->getHasError()) {
            $normalizedAddress = $serviceResult->getAddress();
            $street[] = $normalizedAddress->getLine1();
            if ($normalizedAddress->getLine2()) {
                $street[] = $normalizedAddress->getLine2();
            }
            $address->setStreet($street);
            $address->setCity($normalizedAddress->getCity());
            $address->setRegion($normalizedAddress->getRegion());
            $address->setPostcode($normalizedAddress->getPostcode());
            $address->setCountry($normalizedAddress->getCountry());
            $address->setData('is_normalized', true);
        }
    }

    /**
     * Is Normalize Address Enabled
     *
     * @return bool
     */
    protected function isNormalizeAddressEnabled()
    {
        return $this->config->getNormalizeAddress($this->storeManager->getStore());
    }
}
