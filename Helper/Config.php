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

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ScopeInterface as AppScopeInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Sales\Model\Order\Shipment;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

/**
 * Class Config
 *
 * @package OnePica\AvaTax\Helper
 */
class Config extends AbstractHelper
{
    /**
     * Module name
     */
    const MODULE_NAME = 'OnePica_AvaTax';

    /**#@+
     * Config xml path
     */
    const AVATAX_ACTIVE_SERVICE                     = 'tax/avatax/active_service';
    const AVATAX_SERVICE_ACTION                     = 'tax/avatax/action';
    const AVATAX_SERVICE_URL                        = 'tax/avatax/url';
    const AVATAX_SERVICE_ACCOUNT_NUMBER             = 'tax/avatax/account_number';
    const AVATAX_SERVICE_LICENCE_KEY                = 'tax/avatax/license_key';
    const AVATAX_SERVICE_COMPANY_CODE               = 'tax/avatax/company_code';
    const AVATAX_SERVICE_ALLOWED_LOG_TYPES          = 'tax/avatax/avatax_log_group/allowed_log_types';
    const AVATAX_SERVICE_LOG_LIFETIME               = 'tax/avatax/avatax_log_group/log_lifetime';
    const AVATAX_SERVICE_QUEUE_SUCCESS_LIFETIME     = 'tax/avatax/avatax_log_group/queue_success_lifetime';
    const AVATAX_SERVICE_QUEUE_FAILED_LIFETIME      = 'tax/avatax/avatax_log_group/queue_failed_lifetime';
    const AVATAX_SERVICE_QUEUE_PROCESS_ITEMS_LIMIT  = 'tax/avatax/avatax_log_group/queue_process_items_limit';
    const AVATAX_VALIDATE_ADDRESS                   = 'tax/avatax/address_validation_group/validate_address';
    const AVATAX_NORMALIZE_ADDRESS                  = 'tax/avatax/address_validation_group/normalize_address';
    const AVATAX_ONEPAGE_NORMALIZE_MESSAGE          = 'tax/avatax/address_validation_group/onepage_normalize_message';
    const AVATAX_MULTIADDRESS_NORMALIZE_MESSAGE     = 'tax/avatax/address_validation_group/multiaddress_normalize_message';
    const AVATAX_VALIDATE_ADDRESS_MESSAGE           = 'tax/avatax/address_validation_group/validate_address_message';
    const AVATAX_ADDRESS_VALIDATION_COUNTRIES       = 'tax/avatax/address_validation_group/address_validation_countries';
    const AVATAX_FIELD_REQUIRED_LIST                = 'tax/avatax/request_settings_group/field_required_list';
    const AVATAX_FIELD_RULE                         = 'tax/avatax/request_settings_group/field_rule';
    const AVATAX_REGION_FILTER_TAXABLE_COUNTRY      = 'tax/avatax/region_filter_group/taxable_country';
    const AVATAX_REGION_FILTER_MODE                 = 'tax/avatax/region_filter_group/region_filter_mode';
    const AVATAX_REGION_FILTER_LIST                 = 'tax/avatax/region_filter_group/region_filter_list';
    /**#@-*/

    /**#@+
     * Data Mapping settings xml path
     */
    const AVATAX_SERVICE_CUSTOMER_CODE_FORMAT     = 'tax/avatax/avatax_data_mapping_group/customer_code_format';
    const AVATAX_SERVICE_SHIPPING_SKU             = 'tax/avatax/avatax_data_mapping_group/shipping_sku';
    const AVATAX_SERVICE_GW_ITEMS_SKU             = 'tax/avatax/avatax_data_mapping_group/gw_items_sku';
    const AVATAX_SERVICE_GW_ORDER_SKU             = 'tax/avatax/avatax_data_mapping_group/gw_order_sku';
    const AVATAX_SERVICE_GW_PRINTED_CARD_SKU      = 'tax/avatax/avatax_data_mapping_group/gw_printed_card_sku';
    const AVATAX_SERVICE_ADJUSTMENTS_POSITIVE_SKU = 'tax/avatax/avatax_data_mapping_group/adjustment_positive_sku';
    const AVATAX_SERVICE_ADJUSTMENTS_NEGATIVE_SKU = 'tax/avatax/avatax_data_mapping_group/adjustment_negative_sku';
    const AVATAX_SERVICE_SALES_PERSON_CODE        = 'tax/avatax/avatax_data_mapping_group/sales_person_code';
    const AVATAX_SERVICE_LOCATION_CODE            = 'tax/avatax/avatax_data_mapping_group/location_code';
    const AVATAX_SERVICE_FIRST_REFERENCE_CODE     = 'tax/avatax/avatax_data_mapping_group/first_reference_code';
    const AVATAX_SERVICE_SECOND_REFERENCE_CODE    = 'tax/avatax/avatax_data_mapping_group/second_reference_code';
    const AVATAX_SERVICE_USE_UPC_CODE             = 'tax/avatax/avatax_data_mapping_group/use_upc_code';
    const AVATAX_SERVICE_UPC_CODE                 = 'tax/avatax/avatax_data_mapping_group/upc_code';
    /**#@-*/

    /**
     * Product metadata
     *
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * Module list
     *
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\App\Helper\Context           $context
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\Module\ModuleListInterface   $moduleList
     */
    public function __construct(
        Context $context,
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList
    ) {
        parent::__construct($context);
        $this->productMetadata = $productMetadata;
        $this->moduleList = $moduleList;
    }

    /**
     * Get active service
     *
     * @return string
     */
    public function getActiveService()
    {
        return $this->scopeConfig->getValue(self::AVATAX_ACTIVE_SERVICE);
    }

    /**
     * Get module version
     *
     * @return string
     */
    public function getModuleVersion()
    {
        return $this->moduleList->getOne(self::MODULE_NAME)['setup_version'];
    }

    /**
     * Get service action
     *
     * @param Store|int $store
     * @return int
     */
    public function getServiceAction($store = null)
    {
        return (int)$this->getConfig(self::AVATAX_SERVICE_ACTION, $store);
    }

    /**
     * Get service url
     *
     * @param Store|int $store
     * @return string
     */
    public function getServiceUrl($store = null)
    {
        return (string)$this->getConfig(self::AVATAX_SERVICE_URL, $store);
    }

    /**
     * Get service account number
     *
     * @param Store|int $store
     * @return string
     */
    public function getServiceAccountNumber($store = null)
    {
        return (string)$this->getConfig(self::AVATAX_SERVICE_ACCOUNT_NUMBER, $store);
    }

    /**
     * Get service licence key
     *
     * @param Store|int $store
     * @return string
     */
    public function getServiceLicenceKey($store = null)
    {
        return (string)$this->getConfig(self::AVATAX_SERVICE_LICENCE_KEY, $store);
    }

    /**
     * Get service company code
     *
     * @param Store|int $store
     * @return string
     */
    public function getServiceCompanyCode($store = null)
    {
        return (string)$this->getConfig(self::AVATAX_SERVICE_COMPANY_CODE, $store);
    }

    /**
     * Get service company code
     *
     * @param Store|int $store
     * @return array
     */
    public function getAllowedLogTypes($store = null)
    {
        return explode(',', $this->getConfig(self::AVATAX_SERVICE_ALLOWED_LOG_TYPES, $store));
    }

    /**
     * Get log lifetime
     *
     * @param Store|int $store
     * @return int
     */
    public function getLogLifetime($store = null)
    {
        return (int)$this->getConfig(self::AVATAX_SERVICE_LOG_LIFETIME, $store);
    }

    /**
     * Get Queue Failed Lifetime
     *
     * @param Store|int $store
     * @return int
     */
    public function getQueueFailedLifetime($store = null)
    {
        return (int)$this->getConfig(self::AVATAX_SERVICE_QUEUE_FAILED_LIFETIME, $store);
    }

    /**
     * Get Queue Success Lifetime
     *
     * @param Store|int $store
     * @return int
     */
    public function getQueueSuccessLifetime($store = null)
    {
        return (int)$this->getConfig(self::AVATAX_SERVICE_QUEUE_SUCCESS_LIFETIME, $store);
    }

    /**
     * Get Queue Process Items Limit
     *
     * @param Store|int $store
     * @return int
     */
    public function getQueueProcessItemsLimit($store = null)
    {
        return (int)$this->getConfig(self::AVATAX_SERVICE_QUEUE_PROCESS_ITEMS_LIMIT, $store);
    }

    /**
     * Get customer code format
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return int
     */
    public function getCustomerCodeFormat($scopeCode = null)
    {
        return (int)$this->getConfig(self::AVATAX_SERVICE_CUSTOMER_CODE_FORMAT, $scopeCode);
    }

    /**
     * Get shipping sku
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return string
     */
    public function getShippingSku($scopeCode = null)
    {
        return (string)$this->getConfig(self::AVATAX_SERVICE_SHIPPING_SKU, $scopeCode);
    }

    /**
     * Get gw items sku
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return string
     */
    public function getGwItemsSku($scopeCode = null)
    {
        return (string)$this->getConfig(self::AVATAX_SERVICE_GW_ITEMS_SKU, $scopeCode);
    }

    /**
     * Get gw order sku
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return string
     */
    public function getGwOrderSku($scopeCode = null)
    {
        return (string)$this->getConfig(self::AVATAX_SERVICE_GW_ORDER_SKU, $scopeCode);
    }

    /**
     * Get gw printed card sku
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return string
     */
    public function getGwPrintedCardSku($scopeCode = null)
    {
        return (string)$this->getConfig(self::AVATAX_SERVICE_GW_PRINTED_CARD_SKU, $scopeCode);
    }

    /**
     * Get adjustments positive sku
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return string
     */
    public function getAdjustmentsPositiveSku($scopeCode = null)
    {
        return (string)$this->getConfig(self::AVATAX_SERVICE_ADJUSTMENTS_POSITIVE_SKU, $scopeCode);
    }

    /**
     * Get adjustments negative sku
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return string
     */
    public function getAdjustmentsNegativeSku($scopeCode = null)
    {
        return (string)$this->getConfig(self::AVATAX_SERVICE_ADJUSTMENTS_NEGATIVE_SKU, $scopeCode);
    }

    /**
     * Get sales person code
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return string
     */
    public function getSalesPersonCode($scopeCode = null)
    {
        return (string)$this->getConfig(self::AVATAX_SERVICE_SALES_PERSON_CODE, $scopeCode);
    }

    /**
     * Get location code
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return string
     */
    public function getLocationCode($scopeCode = null)
    {
        return (string)$this->getConfig(self::AVATAX_SERVICE_LOCATION_CODE, $scopeCode);
    }

    /**
     * Get First reference code
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return string
     */
    public function getFirstReferenceCode($scopeCode = null)
    {
        return (string)$this->getConfig(self::AVATAX_SERVICE_FIRST_REFERENCE_CODE, $scopeCode);
    }

    /**
     * Get second reference code
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return string
     */
    public function getSecondReferenceCode($scopeCode = null)
    {
        return (string)$this->getConfig(self::AVATAX_SERVICE_SECOND_REFERENCE_CODE, $scopeCode);
    }

    /**
     * Get use upc code
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return bool
     */
    public function getUseUpcCode($scopeCode = null)
    {
        return (bool)$this->getConfig(self::AVATAX_SERVICE_USE_UPC_CODE, $scopeCode);
    }

    /**
     * Get upc code
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return string
     */
    public function getUpcCode($scopeCode = null)
    {
        return (string)$this->getConfig(self::AVATAX_SERVICE_UPC_CODE, $scopeCode);
    }

    /**
     * Get origin first street line
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return string
     */
    public function getOriginFirstStreetLine($scopeCode = null)
    {
        return (string)$this->getConfig(Shipment::XML_PATH_STORE_ADDRESS1, $scopeCode);
    }

    /**
     * Get origin second street line
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return string
     */
    public function getOriginSecondStreetLine($scopeCode = null)
    {
        return (string)$this->getConfig(Shipment::XML_PATH_STORE_ADDRESS2, $scopeCode);
    }

    /**
     * Get origin city
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return string
     */
    public function getOriginCity($scopeCode = null)
    {
        return (string)$this->getConfig(Shipment::XML_PATH_STORE_CITY, $scopeCode);
    }

    /**
     * Get origin region id
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return int
     */
    public function getOriginRegionId($scopeCode = null)
    {
        return (int)$this->getConfig(Shipment::XML_PATH_STORE_REGION_ID, $scopeCode);
    }

    /**
     * Get origin country id
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return string
     */
    public function getOriginCountryId($scopeCode = null)
    {
        return (string)$this->getConfig(Shipment::XML_PATH_STORE_COUNTRY_ID, $scopeCode);
    }

    /**
     * Get origin zip
     *
     * @param null|string|AppScopeInterface $scopeCode
     * @return string
     */
    public function getOriginZip($scopeCode = null)
    {
        return (string)$this->getConfig(Shipment::XML_PATH_STORE_ZIP, $scopeCode);
    }

    /**
     * Get magento edition
     *
     * @return string
     */
    public function getMagentoEdition()
    {
        return $this->productMetadata->getEdition();
    }

    /**
     * Get user agent
     *
     * @return string
     */
    public function getUserAgent()
    {
        $userAgent = $this->productMetadata->getName() . ' ';
        $userAgent .= $this->getMagentoEdition() . ' v';
        $userAgent .= $this->productMetadata->getVersion() . ' ';
        $userAgent .= self::MODULE_NAME . ' v' . $this->getModuleVersion();

        return $userAgent;
    }

    /**
     * Get config
     *
     * @param string    $path
     * @param Store|int $store
     * @param string    $scopeType
     * @return mixed
     */
    protected function getConfig($path, $store = null, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue($path, $scopeType, $store);
    }

    /**
     * Get validate address
     *
     * @param Store|int $store
     * @return int
     */
    public function getValidateAddress($store = null)
    {
        return (int)$this->getConfig(self::AVATAX_VALIDATE_ADDRESS, $store);
    }

    /**
     * Get normalize address
     *
     * @param Store|int $store
     * @return int
     */
    public function getNormalizeAddress($store = null)
    {
        return (int)$this->getConfig(self::AVATAX_NORMALIZE_ADDRESS, $store);
    }

    /**
     * Get onepage normalize message
     *
     * @param Store|int $store
     * @return string
     */
    public function getOnepageNormalizeMessage($store = null)
    {
        return (string)$this->getConfig(self::AVATAX_ONEPAGE_NORMALIZE_MESSAGE, $store);
    }

    /**
     * Get multiaddress normalize message
     *
     * @param Store|int $store
     * @return string
     */
    public function getMultiaddressNormalizeMessage($store = null)
    {
        return (string)$this->getConfig(self::AVATAX_MULTIADDRESS_NORMALIZE_MESSAGE, $store);
    }

    /**
     * Get validate address message
     *
     * @param Store|int $store
     * @return string
     */
    public function getValidateAddressMessage($store = null)
    {
        return (string)$this->getConfig(self::AVATAX_VALIDATE_ADDRESS_MESSAGE, $store);
    }

    /**
     * Get field required list
     *
     * @param Store|int $store
     * @return string
     */
    public function getFieldRequiredList($store = null)
    {
        return (string)$this->getConfig(self::AVATAX_FIELD_REQUIRED_LIST, $store);
    }

    /**
     * Get field rule
     *
     * @param Store|int $store
     * @return string
     */
    public function getFieldRule($store = null)
    {
        return (string)$this->getConfig(self::AVATAX_FIELD_RULE, $store);
    }

    /**
     * Get Region Filter Taxable Countries
     *
     * @param Store|int $store
     * @return string
     */
    public function getRegionFilterTaxableCountries($store = null)
    {
        return (string)$this->getConfig(self::AVATAX_REGION_FILTER_TAXABLE_COUNTRY, $store);
    }

    /**
     * Get Address Validation Countries
     *
     * @return string
     */
    public function getAddressValidationCountries()
    {
        return (string)$this->getConfig(self::AVATAX_ADDRESS_VALIDATION_COUNTRIES);
    }

    /**
     * Get region filter mode
     *
     * @param Store|int $store
     * @return int
     */
    public function getRegionFilterMode($store = null)
    {
        return (int)$this->getConfig(self::AVATAX_REGION_FILTER_MODE, $store);
    }

    /**
     * Get region filter fist
     *
     * @param Store|int $store
     * @return string
     */
    public function getRegionFilterList($store = null)
    {
        return (string)$this->getConfig(self::AVATAX_REGION_FILTER_LIST, $store);
    }
}
