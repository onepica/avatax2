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
namespace Astound\AvaTax\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\Message\MessageInterface;
use Magento\Store\Model\Store;
use Magento\Framework\App\Helper\Context;
use Magento\Directory\Model\RegionFactory;
use Astound\AvaTax\Helper\Data as AvataxDataHelper;
use Astound\AvaTax\Model\Service\Result\Storage\Filter;
use Astound\AvaTax\Model\Source\Avatax16\Action as AvataxActionSource;
use Astound\AvaTax\Api\Service\LoggerInterface;
use Astound\AvaTax\Model\Log;
use Astound\AvaTax\Model\Service\Result\Base;
use Astound\AvaTax\Model\Service\ConfigRepositoryInterface;

/**
 * Class Address
 *
 * @package Astound\AvaTax\Helper
 */
class Address extends AbstractHelper
{
    /**
     * Object Manager
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Result Storage
     *
     * @var Filter
     */
    protected $resultStorage;

    /**
     * Service logger
     *
     * @var \Astound\AvaTax\Api\Service\LoggerInterface
     */
    protected $logger;

    /**
     * Config repository
     *
     * @var \Astound\AvaTax\Model\Service\ConfigRepositoryInterface
     */
    protected $configRepository;

    /**
     * Region Factory
     *
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;

    /**
     * Message manager object
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param Context                                                 $context
     * @param Config                                                  $config
     * @param ObjectManagerInterface                                  $objectManager
     * @param Filter                                                  $resultStorage
     * @param LoggerInterface                                         $logger
     * @param \Astound\AvaTax\Model\Service\ConfigRepositoryInterface $configRepository
     * @param RegionFactory                                           $regionFactory
     * @param \Magento\Framework\Message\ManagerInterface             $messageManager
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        Config $config,
        Filter $resultStorage,
        LoggerInterface $logger,
        ConfigRepositoryInterface $configRepository,
        RegionFactory $regionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->objectManager = $objectManager;
        $this->resultStorage = $resultStorage;
        $this->logger = $logger;
        $this->configRepository = $configRepository;
        $this->regionFactory = $regionFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * Determines if the address should be filtered
     *
     * @param AbstractAddress $address
     * @param Store $store
     * @param int $filterMode
     * @param bool $isAddressValidation
     * @return bool
     */
    public function isAddressActionable(
        AbstractAddress $address,
        Store $store = null,
        $filterMode = AvataxDataHelper::REGION_FILTER_MODE_ALL,
        $isAddressValidation = false
    ) {
        if ($this->config->getServiceAction($store) == AvataxActionSource::ACTION_DISABLE) {
            return false;
        }

        // filter by country
        $filter = $this->filterByCountry($address, $store, $isAddressValidation);

        if (!$filter) {
            // filter by region
            $filter = $this->filterByRegion($address, $store, $isAddressValidation, $filterMode);
        }

        if ($filter) {
            $this->logFilter($address, $store, $filter, $filterMode);
        }

        // if we have filter then the address is not actionable
        return $filter ? false : true;
    }

    /**
     * Filter by region
     *
     * @param AbstractAddress $address
     * @param Store $store
     * @param bool $isAddressValidation
     * @param string $filterMode
     * @return string|bool
     */
    protected function filterByRegion(
        AbstractAddress $address,
        Store $store = null,
        $isAddressValidation,
        $filterMode
    ) {
        $filter = false;
        $regionFilterModeByStore = $this->getRegionFilterModByStore($store);

        if ($regionFilterModeByStore >= $filterMode) {
            // get region filter
            $filter = $this->getFilterRegion($address, $store);
        }

        if ($isAddressValidation
            && $filter
            && ($regionFilterModeByStore !== AvataxDataHelper::REGION_FILTER_MODE_ALL)
        ) {
            // disable filter by region for address validation for not REGION_FILTER_MODE_ALL
            $filter = false;
        }

        return $filter;
    }

    /**
     * Filter by country
     *
     * @param AbstractAddress $address
     * @param Store $store
     * @param bool $isAddressValidation
     * @return string|bool
     */
    protected function filterByCountry(
        AbstractAddress $address,
        Store $store = null,
        $isAddressValidation
    ) {
        $filter = false;
        $countryId = $address->getCountryId();
        if (!in_array($countryId, $this->getTaxableCountryByStore($store))
            || ($isAddressValidation && !in_array($countryId, $this->getAddressValidationCountries()))
        ) {
            $filter = 'country';
        }

        return $filter;
    }

    /**
     * Log filter
     *
     * @param AbstractAddress $address
     * @param Store $store
     * @param string $filter
     * @param string $filterMode
     * @return $this
     */
    protected function logFilter(
        AbstractAddress $address,
        Store $store = null,
        $filter,
        $filterMode
    ) {
        if ($this->resultStorage->getResult($address->format('text'))) {
            return $this;
        }

        $addressData = $address->debug();

        $config = $this->configRepository->getConfigByStore($store);
        /** @var Base $result */
        $result = $this->objectManager->create(Base::class);
        $type = ($filterMode == AvataxDataHelper::REGION_FILTER_MODE_TAX)
            ? 'tax_calc'
            : 'tax_calc|address_opts';
        $resultStr = 'filter: ' . $filter . ', type: ' . $type;
        $result->setResponse(['result' => $resultStr]);

        $this->resultStorage->setResult($address->format('text'), $result);

        $this->logger->log(
            Log::FILTER,
            $addressData,
            $result,
            $store->getId(),
            $config->getConnection()
        );

        return $this;
    }

    /**
     * Get region filter mod by store
     *
     * @param Store $store
     * @return int
     */
    public function getRegionFilterModByStore($store)
    {
        return $this->config->getRegionFilterMode($store);
    }

    /**
     * Get region filter
     *
     * @param AbstractAddress $address
     * @param Store $store
     * @return string|bool
     */
    protected function getFilterRegion(
        AbstractAddress $address,
        Store $store
    ) {
        $filter        = false;
        $regionFilters = explode(',', $this->config->getRegionFilterList($store));
        $entityId      = $address->getRegionId();
        if (!in_array($entityId, $regionFilters)) {
            $filter = 'region';
        }
        return $filter;
    }

    /**
     * Get taxable country by store
     *
     * @param Store $store
     * @return array
     */
    public function getTaxableCountryByStore($store)
    {
        return explode(',', $this->config->getRegionFilterTaxableCountries($store));
    }

    /**
     * Get taxable country by store
     *
     * @return array
     */
    public function getAddressValidationCountries()
    {
        return explode(',', $this->config->getAddressValidationCountries());
    }

    /**
     * Determines if the object (invoice, credit memo) should use AvaTax services
     *
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $object
     * @return bool
     */
    public function isObjectActionable($object)
    {
        $store = $object->getStore();

        $action = $object->getOrder() ? AvataxActionSource::ACTION_CALC_SUBMIT : AvataxActionSource::ACTION_CALC;
        if ($this->config->getServiceAction($store) < $action) {
            return false;
        }

        $shippingAddress = $object->getShippingAddress()
                         ? $object->getShippingAddress()
                         : $object->getBillingAddress();

        $address = $this->convertOrderAddressToCustomerAddress($shippingAddress);

        return $this->isAddressActionable($address, $store, AvataxDataHelper::REGION_FILTER_MODE_TAX);
    }

    /**
     * Convert Order Address to Customer Address
     *
     * @param \Magento\Sales\Model\Order\Address $address
     * @return \Magento\Customer\Model\Address
     */
    protected function convertOrderAddressToCustomerAddress(
        \Magento\Sales\Model\Order\Address $address
    ) {
        $addressData = $address->toArray();
        $customerAddress = $this->objectManager->create('\Magento\Customer\Model\Address');
        $customerAddress->setData($addressData);

        return $customerAddress;
    }

    /**
     * Get region
     *
     * @param string $region
     * @param string $country
     * @return \Magento\Directory\Model\Region|null
     */
    public function getRegion($region, $country)
    {
        $regionObj = $this->regionFactory->create();
        $regionObj->loadByCode($region, $country);
        if (!$regionObj->getId()) {
            $regionObj->loadByName($region, $country);
        }

        return $regionObj->getId() ? $regionObj : null;
    }

    /**
     * Add validation notice
     *
     * @param string $message
     * @return $this
     */
    public function addValidationNotice($message)
    {
        $messageObject = $this->messageManager->createMessage(
            MessageInterface::TYPE_NOTICE,
            AvataxDataHelper::MESSAGE_GROUP_CODE
        );
        $messageObject->setText($message);
        $this->messageManager->addMessage($messageObject);

        return $this;
    }
}
