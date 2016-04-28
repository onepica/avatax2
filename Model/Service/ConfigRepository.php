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
 * @author     OnePica Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax\Model\Service;

use Magento\Store\Model\Store;
use OnePica\AvaTax\Model\Service\Avatax16\ConfigInterface;

/**
 * Class ConfigRepository
 *
 * @package OnePica\AvaTax\Model\Service
 */
class ConfigRepository implements ConfigRepositoryInterface
{
    /**
     * Config storage
     *
     * @var ConfigInterface[]
     */
    protected $configStorage = [];

    /**
     * Config factory
     *
     * @var \OnePica\AvaTax\Model\Service\ConfigFactory
     */
    protected $configFactory;

    /**
     * ConfigRepository constructor.
     *
     * @param \OnePica\AvaTax\Model\Service\ConfigFactory $configFactory
     */
    public function __construct(ConfigFactory $configFactory)
    {
        $this->configFactory = $configFactory;
    }

    /**
     * Get service config by store
     *
     * @param \Magento\Store\Model\Store $store
     * @return ConfigInterface
     */
    public function getConfigByStore(Store $store)
    {
        if (!isset($this->configStorage[$store->getCode()])) {
            $this->initNewConfig($store);
        }

        return $this->configStorage[$store->getCode()];
    }

    /**
     * Init config
     *
     * @param \Magento\Store\Model\Store $store
     * @return $this
     */
    protected function initNewConfig(Store $store)
    {
        $this->configStorage[$store->getCode()] = $this->configFactory->create($store);

        return $this;
    }
}
