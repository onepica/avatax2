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
namespace OnePica\AvaTax\Model\Service\Avatax16;

use Magento\Store\Model\Store;
use OnePica\AvaTax\Model\Service\Avatax16\ConfigInterface;
use OnePica\AvaTax\Helper\Config as ConfigHelper;
use OnePica\AvaTax16\Config as LibConfig;
use OnePica\AvaTax16\TaxService;

/**
 * Class Config
 *
 * @package OnePica\AvaTax\Model\Service\Avatax16
 */
class Config implements ConfigInterface
{
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
        $config->setAuthorizationHeader($this->config->getServiceLicenceKey($this->store));
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
