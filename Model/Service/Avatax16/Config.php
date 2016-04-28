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
namespace Astound\AvaTax\Model\Service\Avatax16;

use Magento\Store\Model\Store;
use Astound\AvaTax\Model\Service\Avatax16\ConfigInterface;
use Astound\AvaTax\Helper\Config as ConfigHelper;
use OnePica\AvaTax16\Config as LibConfig;
use OnePica\AvaTax16\TaxService;

/**
 * Class Config
 *
 * @package Astound\AvaTax\Model\Service\Avatax16
 */
class Config implements ConfigInterface
{
    /**
     * Authorization header prefix
     */
    const AUTHORIZATION_HEADER_PREFIX = 'AvalaraAuth ';

    /**
     * Connection
     *
     * @var \OnePica\AvaTax16\TaxService
     */
    protected $connection;

    /**
     * Store
     *
     * @var Store
     */
    protected $store;

    /**
     * Config helper
     *
     * @var ConfigHelper
     */
    protected $config;

    /**
     * Lib config
     *
     * @var LibConfig
     */
    protected $libConfig;

    /**
     * Config constructor.
     *
     * @param \Magento\Store\Model\Store $store
     * @param ConfigHelper               $config
     */
    public function __construct(Store $store, ConfigHelper $config)
    {
        $this->store = $store;
        $this->config = $config;
        $this->init();
    }

    /**
     * Init config
     *
     * @return $this
     */
    protected function init()
    {
        $config = $this->createLibConfig();
        $config->setBaseUrl($this->config->getServiceUrl($this->store));
        $config->setAccountId($this->config->getServiceAccountNumber($this->store));
        $config->setCompanyCode($this->config->getServiceCompanyCode($this->store));
        $authorizationHeader = self::AUTHORIZATION_HEADER_PREFIX
                             . $this->config->getServiceLicenceKey($this->store);
        $config->setAuthorizationHeader($authorizationHeader);
        $config->setUserAgent($this->config->getUserAgent());
        $this->libConfig = $config;
        $this->connection = $this->getLibTaxService($config);

        return $this;
    }

    /**
     * Get connection
     *
     * @return \OnePica\AvaTax16\TaxService
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Get lib config
     *
     * @return \OnePica\AvaTax16\Config
     */
    public function getLibConfig()
    {
        return $this->libConfig;
    }

    /**
     * Create lib config class instance
     *
     * @return LibConfig
     */
    protected function createLibConfig()
    {
        return new LibConfig();
    }

    /**
     * Get tax service instance
     *
     * @param LibConfig $config
     * @return \OnePica\AvaTax16\TaxService
     */
    protected function getLibTaxService(LibConfig $config)
    {
        return new TaxService($config);
    }
}
