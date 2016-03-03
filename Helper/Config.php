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
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

/**
 * Class Config
 *
 * @package OnePica\AvaTax\Helper
 */
class Config extends AbstractHelper
{
    /**#@+
     * Config xml path
     */
    const AVATAX_ACTIVE_SERVICE                 = 'tax/avatax/active_service';
    const AVATAX_SERVICE_ACTION                 = 'tax/avatax/action';
    const AVATAX_SERVICE_URL                    = 'tax/avatax/url';
    const AVATAX_SERVICE_ACCOUNT_NUMBER         = 'tax/avatax/account_number';
    const AVATAX_SERVICE_LICENCE_KEY            = 'tax/avatax/license_key';
    const AVATAX_SERVICE_COMPANY_CODE           = 'tax/avatax/company_code';
    const AVATAX_VALIDATE_ADDRESS               = 'tax/avatax/validate_address';
    const AVATAX_NORMALIZE_ADDRESS              = 'tax/avatax/normalize_address';
    const AVATAX_ONEPAGE_NORMALIZE_MESSAGE      = 'tax/avatax/onepage_normalize_message';
    const AVATAX_MULTIADDRESS_NORMALIZE_MESSAGE = 'tax/avatax/multiaddress_normalize_message';
    const AVATAX_VALIDATE_ADDRESS_MESSAGE       = 'tax/avatax/validate_address_message';
    const AVATAX_FIELD_REQUIRED_LIST            = 'tax/avatax/field_required_list';
    const AVATAX_FIELD_RULE                     = 'tax/avatax/field_rule';
    /**#@-*/

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
}