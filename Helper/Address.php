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
namespace OnePica\AvaTax\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Store\Model\Store;
use Magento\Framework\App\Helper\Context;
use OnePica\AvaTax\Helper\Data as AvataxDataHelper;
use OnePica\AvaTax\Model\Service\Result\Storage\Filter;
use OnePica\AvaTax\Model\Source\Avatax16\Action as AvataxActionSource;
use OnePica\AvaTax\Api\Service\LoggerInterface;
use OnePica\AvaTax\Model\Log;
use OnePica\AvaTax\Model\Service\Result\Base;
use OnePica\AvaTax\Api\ConfigRepositoryInterface;

/**
 * Class Address
 *
 * @package OnePica\AvaTax\Helper
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
     * @var \OnePica\AvaTax\Api\Service\LoggerInterface
     */
    protected $logger;

    /**
     * Config repository
     *
     * @var \OnePica\AvaTax\Api\ConfigRepositoryInterface
     */
    protected $configRepository;

    /**
     * @param Context $context
     * @param Config $config
     * @param ObjectManagerInterface $objectManager
     * @param Filter $resultStorage
     * @param LoggerInterface $logger
     * @param ConfigRepositoryInterface $configRepository
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        Config $config,
        Filter $resultStorage,
        LoggerInterface $logger,
        ConfigRepositoryInterface $configRepository
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->objectManager = $objectManager;
        $this->resultStorage = $resultStorage;
        $this->logger = $logger;
        $this->configRepository = $configRepository;
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
}
