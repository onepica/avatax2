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
namespace Astound\AvaTax\Plugin\Quote\Model\Quote;

use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Astound\AvaTax\Helper\Config;
use Astound\AvaTax\Model\Tool\Validate;
use Astound\AvaTax\Model\Service\Request\Address as RequestAddress;
use Astound\AvaTax\Model\Service\Result\AddressValidation;
use Astound\AvaTax\Helper\Data as AvataxDataHelper;
use Astound\AvaTax\Helper\Address as AvataxAddressHelper;

/**
 * Quote address validator
 *
 * @package Astound\AvaTax\Plugin\Quote\Model\Quote\AddressValidator
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
     * Message manager object
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Address helper
     *
     * @var AvataxAddressHelper
     */
    protected $addressHelper;

    /**
     * Store
     *
     * @var \Magento\Store\Model\Store
     */
    protected $store;

    /**
     * ShippingInformationManagement constructor
     *
     * @param Config $config
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param AvataxAddressHelper $addressHelper
     */
    public function __construct(
        Config $config,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        AvataxAddressHelper $addressHelper
    ) {
        $this->config = $config;
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->addressHelper = $addressHelper;
    }

    /**
     * Init Store
     *
     * @param AbstractAddress $address
     * @return $this
     */
    protected function initStore(AbstractAddress $address)
    {
        $storeId = $address->getQuote()
                 ? $address->getQuote()->getStore()->getId()
                 : null;

        $this->store = $this->storeManager->getStore($storeId);

        return $this;
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
        $this->initStore($address);

        if (!$this->isValidationRequired($address)) {
            return true;
        }

        $addressFieldsErrors = $this->checkAddressFields($address);
        if ($addressFieldsErrors) {
            $addressFieldsErrors = $this->processErrors($addressFieldsErrors);
            // We have errors so we will not continue address validation by AvaTax service
            return $addressFieldsErrors ? $addressFieldsErrors : true;
        }

        $requestAddress = $this->convertAddressToRequestAddress($address);
        $validator = $this->objectManager->create(Validate::class, ['object' => $requestAddress]);
        $serviceResult = $validator->execute();
        $this->normalizeAddress($address, $serviceResult);

        $result = $serviceResult->getErrors() ? $serviceResult->getErrors() : true;
        if ($result === true) {
            return true;
        }

        $errors = $this->processErrors($result);

        return empty($errors) ? true : $errors;
    }

    /**
     * Process Errors
     *
     * @param array $errors
     * @return array|null
     */
    protected function processErrors(array $errors)
    {
        $returnErrors = [];
        foreach ($errors as $message) {
            if ($this->getValidateAddressMode() == AvataxDataHelper::SHIPPING_ADDRESS_VALIDATION_ALLOW) {
                $this->addNotice($message);
            } elseif ($this->getValidateAddressMode() == AvataxDataHelper::SHIPPING_ADDRESS_VALIDATION_PREVENT) {
                $returnErrors[] = $message;
            }
        }

        return !empty($returnErrors) ? $returnErrors : null;
    }


    /**
     * Is validation required
     *
     * @param AbstractAddress $address
     * @return bool
     */
    protected function isValidationRequired(AbstractAddress $address)
    {
        $isShippingAddress = $address->getAddressType() == \Magento\Quote\Model\Quote\Address::ADDRESS_TYPE_SHIPPING;
        if ($this->getValidationMode()
            && ($isShippingAddress || $address->getQuote()->isVirtual())
            && $this->addressHelper->isAddressActionable(
                $address,
                $this->store,
                AvataxDataHelper::REGION_FILTER_MODE_ALL,
                true
            )
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
        return $this->config->getValidateAddress($this->store);
    }

    /**
     * Convert Address To Request Address
     *
     * @param AbstractAddress $address
     * @return RequestAddress
     */
    protected function convertAddressToRequestAddress(AbstractAddress $address)
    {
        $requestAddress = $this->objectManager->create('Astound\AvaTax\Model\Service\Request\Address');
        $requestAddress->setStore($this->store);
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
     * @param AbstractAddress   $address
     * @param AddressValidation $serviceResult
     * @return $this
     */
    protected function normalizeAddress(AbstractAddress $address, AddressValidation $serviceResult)
    {
        if ($this->isNormalizeAddressEnabled()
            && !$serviceResult->getHasError()
            && $serviceResult->getResolution()
        ) {
            $addressWasChanged = false;
            $normalizedAddress = $serviceResult->getAddress();
            $street[] = $normalizedAddress->getLine1();
            if ($normalizedAddress->getLine2()) {
                $street[] = $normalizedAddress->getLine2();
            }

            if (!empty(array_diff($street, $address->getStreet()))) {
                $address->setStreet($street);
                $addressWasChanged = true;
            }

            if ($address->getCity() != $normalizedAddress->getCity()) {
                $address->setCity($normalizedAddress->getCity());
                $addressWasChanged = true;
            }

            $region = $this->addressHelper->getRegion($normalizedAddress->getRegion(), $address->getCountry());
            if ($region && $address->getRegion() != $region->getName()) {
                $address->setRegion($region->getName());
                $address->setRegionId($region->getRegionId());
                $addressWasChanged = true;
            }

            if ($address->getPostcode() != $normalizedAddress->getPostcode()) {
                $address->setPostcode($normalizedAddress->getPostcode());
                $addressWasChanged = true;
            }

            if ($address->getCountry() != $normalizedAddress->getCountry()) {
                $address->setCountry($normalizedAddress->getCountry());
                $addressWasChanged = true;
            }

            if ($addressWasChanged) {
                $address->setData('is_normalized', true);
            }
        }

        return $this;
    }

    /**
     * Is Normalize Address Enabled
     *
     * @return bool
     */
    protected function isNormalizeAddressEnabled()
    {
        return $this->config->getNormalizeAddress($this->store);
    }

    /**
     * Add notice
     *
     * @param string $message
     * @return $this
     */
    protected function addNotice($message)
    {
        $this->addressHelper->addValidationNotice($message);

        return $this;
    }

    /**
     * Get Validate Address Mode
     *
     * @return int
     */
    protected function getValidateAddressMode()
    {
        return $this->config->getValidateAddress($this->store);
    }

    /**
     * Get Onepage Normalize Message
     *
     * @return string
     */
    protected function getOnepageNormalizeMessage()
    {
        return $this->config->getOnepageNormalizeMessage($this->store);
    }

    /**
     * Check Address Fields
     *
     * @param AbstractAddress $address
     * @return array|null
     */
    protected function checkAddressFields(AbstractAddress $address)
    {
        $errors = null;
        $requiredFieldsStr = $this->config->getFieldRequiredList($this->store);
        if ($requiredFieldsStr) {
            $requiredFields = explode(",", $requiredFieldsStr);
            $fieldRules = explode(",", $this->config->getFieldRule($this->store));
            $countryFactory = $this->objectManager->get('\Magento\Directory\Model\CountryFactory');

            foreach ($requiredFields as $field) {
                $hasError = false;
                switch ($field) {
                    case 'country_id':
                        $fieldValue = $countryFactory->create()->loadByCode($address->getCountry())->getName();
                        $field = __('Country');
                        break;
                    case 'region':
                        $fieldValue = $address->getRegion();
                        break;
                    case 'street':
                        $fieldValue = $address->getStreet();
                        break;
                    default:
                        $fieldValue = $address->getData($field);
                        break;
                }

                foreach ($fieldRules as $rule) {
                    if ($this->hasAddressFieldRuleError($fieldValue, $rule)) {
                        $hasError = true;
                        break;
                    }
                }
                if ($hasError) {
                    $errors[] = __('Invalid ') . __($field);
                }
            }
        }

        return $errors;
    }

    /**
     * Has Address Field Rule Error
     *
     * @param string|array $fieldValue
     * @param string $rule
     * @return bool
     */
    protected function hasAddressFieldRuleError($fieldValue, $rule)
    {
        $hasError = false;

        if (is_array($fieldValue)) {
            // fix for street
            foreach ($fieldValue as $value) {
                if ($value == $rule) {
                    $hasError = true;
                    break;
                }
            }
        } else {
            if (!$fieldValue || $fieldValue == $rule) {
                $hasError = true;
            }
        }

        return $hasError;
    }
}
