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
    const AVATAX_ACTIVE_SERVICE            = 'tax/avatax/active_service';
    const AVATAX_SERVICE_ACTION            = 'tax/avatax/action';
    const AVATAX_SERVICE_URL               = 'tax/avatax/url';
    const AVATAX_SERVICE_ACCOUNT_NUMBER    = 'tax/avatax/account_number';
    const AVATAX_SERVICE_LICENCE_KEY       = 'tax/avatax/license_key';
    const AVATAX_SERVICE_COMPANY_CODE      = 'tax/avatax/company_code';
    const AVATAX_SERVICE_ALLOWED_LOG_TYPES = 'tax/avatax/allowed_log_types';
    /**#@-*/

    /**
     * Product metadata
     *
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\App\Helper\Context           $context
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(Context $context, ProductMetadataInterface $productMetadata)
    {
        parent::__construct($context);
        $this->productMetadata = $productMetadata;
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
     * Get user agent
     *
     * @return string
     */
    public function getUserAgent()
    {
        $userAgent = $this->productMetadata->getName() . ' ';
        $userAgent .= $this->productMetadata->getEdition() . ' v';
        $userAgent .= $this->productMetadata->getVersion();

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
}
