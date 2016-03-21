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
use OnePica\AvaTax\Model\Source\Avatax16\Action as AvataxActionSource;
use OnePica\AvaTax\Api\Service\CacheStorageInterface;
use OnePica\AvaTax\Api\Service\LoggerInterface;
use OnePica\AvaTax\Model\Log;
use OnePica\AvaTax\Model\Service\Result\BaseResult;
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
     * Cache Storage
     *
     * @var CacheStorageInterface
     */
    protected $cacheStorage;

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
     * @param CacheStorageInterface $cacheStorage
     * @param LoggerInterface $logger
     * @param ConfigRepositoryInterface $configRepository
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        Config $config,
        CacheStorageInterface $cacheStorage,
        LoggerInterface $logger,
        ConfigRepositoryInterface $configRepository
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->objectManager = $objectManager;
        $cacheStorage->setCacheId('AddressFilter');
        $this->cacheStorage = $cacheStorage;
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

        $filter = false;
        $regionFilterModeByStore = $this->getRegionFilterModByStore($store);

        if ($regionFilterModeByStore >= $filterMode) {
            // filter by region
            $filter = $this->getFilterRegion($address, $store);
        }

        if ($isAddressValidation
            && $filter
            && ($regionFilterModeByStore !== AvataxDataHelper::REGION_FILTER_MODE_ALL)
        ) {
            // disable filter by region for address validation for not REGION_FILTER_MODE_ALL
            $filter = false;
        }

        // filter by country
        $countryId = $address->getCountryId();
        if (!in_array($countryId, $this->getTaxableCountryByStore($store))
            || ($isAddressValidation && !in_array($countryId, $this->getAddressValidationCountries()))
        ) {
            $filter = 'country';
        }

        if ($filter) {
            $hash = $this->cacheStorage->generateHashKeyForData($address->format('text'));
            if (!$this->cacheStorage->get($hash)) {
                $addressData = $address->debug();
                $this->cacheStorage->put($hash, $addressData);
                $config = $this->configRepository->getConfigByStore($store);
                $result = $this->objectManager->create(BaseResult::Class);
                $type = ($filterMode == AvataxDataHelper::REGION_FILTER_MODE_TAX) ? 'tax_calc' : 'tax_calc|address_opts';
                $resultStr = 'filter: ' . $filter . ', type: ' . $type;
                $result->setResponse(['result' => $resultStr]);
                $this->logger->log(
                    Log::FILTER,
                    $addressData,
                    $result,
                    $store->getId(),
                    $config->getConnection());
            }
        }

        return $filter ? false : true;
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
        Store $store)
    {
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