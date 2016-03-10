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
namespace OnePica\AvaTax\Model\Service;

use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\Store;
use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Model\Tool\Validate;
use OnePica\AvaTax\Model\Service\Request\Address as RequestAddress;

/**
 * Class Validator
 *
 * @package OnePica\AvaTax\Model\Service\Validator
 */
class Validator
{
    /**
     * Store id
     *
     * @var int
    */
    protected $storeId;

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
     * ShippingInformationManagement constructor
     *
     * @param Config $config
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Config $config,
        ObjectManagerInterface $objectManager
    ) {
        $this->config = $config;
        $this->objectManager = $objectManager;
    }

    /**
     * Set Store Id
     *
     * @param int $storeId
     */
    public function setStoreId($storeId) {
        $this->storeId = $storeId;
    }

    /**
     * Get Store Id
     *
     * @return int $storeId
     */
    public function getStoreId() {
        return $this->storeId ? $this->storeId : Store::DEFAULT_STORE_ID;
    }

    /**
     * Validate customer address
     *
     * @param AbstractAddress $address
     * @return true|Array
     */
    public function validate(AbstractAddress $address)
    {
        $isValidationEnabled = $this->config->getValidateAddress($this->getStoreid());
        if (!$isValidationEnabled) {
            return true;
        }
        $requestAddress = $this->convertAddressToRequestAddress($address);
        $validator = $this->objectManager->create(Validate::class, ['object' => $requestAddress]);
        $serviceResult = $validator->execute();
        $result = $serviceResult->getErrors() ? $serviceResult->getErrors() : true;

        return $result;
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
        $store = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($this->getStoreid());
        $requestAddress->setStore($store);
        $requestAddress->setLine1($address->getStreetLine(1));
        $requestAddress->setLine2($address->getStreetLine(2));
        $requestAddress->setCity($address->getCity());
        $requestAddress->setRegion($address->getRegion());
        $requestAddress->setPostcode($address->getPostcode());
        $requestAddress->setCountry($address->getCountry());

        return $requestAddress;
    }
}
