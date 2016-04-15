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
use Magento\Store\Model\StoreManagerInterface;
use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Model\Tool\Validate;
use OnePica\AvaTax\Model\Service\Request\Address as RequestAddress;
use OnePica\AvaTax\Model\Service\Result\AddressValidation;
use OnePica\AvaTax\Helper\Data as AvataxDataHelper;
use OnePica\AvaTax\Helper\Address as AvataxAddressHelper;

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
     * Address helper
     *
     * @var AvataxAddressHelper
     */
    protected $addressHelper;

    /**
     * ShippingInformationManagement constructor
     *
     * @param Config $config
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param AvataxAddressHelper $addressHelper
     */
    public function __construct(
        Config $config,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        AvataxAddressHelper $addressHelper
    ) {
        $this->config = $config;
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
        $this->addressHelper = $addressHelper;
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
                $this->storeManager->getStore(),
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
        return $this->config->getNormalizeAddress($this->storeManager->getStore());
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
        return $this->config->getValidateAddress($this->storeManager->getStore());
    }

    /**
     * Get Onepage Normalize Message
     *
     * @return string
     */
    protected function getOnepageNormalizeMessage()
    {
        return $this->config->getOnepageNormalizeMessage($this->storeManager->getStore());
    }

    /**
     * Check Address Fields
     *
     * @param AbstractAddress $address
     * @return array|null
     */
    protected function checkAddressFields(AbstractAddress $address)
    {
        $store = $this->storeManager->getStore();
        $requiredFields = explode(",", $this->config->getFieldRequiredList($store));
        $fieldRules = explode(",", $this->config->getFieldRule($store));
        $errors = null;
        $countryFactory = $this->objectManager->get('\Magento\Directory\Model\CountryFactory');

        foreach ($requiredFields as $field) {

            switch ($field) {
                case 'country_id':
                    $fieldValue = $countryFactory->create()->loadByCode($address->getCountry())->getName();
                    $field = __('Country ');
                    break;
                case 'region':
                    $fieldValue = $address->getRegion();
                    break;
                default:
                    $fieldValue = $address->getData($field);
                    break;
            }

            foreach ($fieldRules as $rule) {
                if ($fieldValue == $rule || !$fieldValue) {
                    $errors[] = __('Invalid ') . __($field);
                }
            }
        }

        return $errors;
    }
}
