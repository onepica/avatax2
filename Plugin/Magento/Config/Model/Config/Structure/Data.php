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
namespace OnePica\AvaTax\Plugin\Magento\Config\Model\Config\Structure;

use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Data
 *
 * @package OnePica\AvaTax\Plugin\Magento\Config\Model\Config\Structure
 */
class Data
{
    /**
     * Cache key
     */
    const CACHE_KEY = 'avatax16_system_disabled';

    /**
     * Config helper
     *
     * @var \OnePica\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Cache
     *
     * @var \Magento\Framework\Config\CacheInterface
     */
    protected $cache;

    /**
     * Data constructor.
     *
     * @param \OnePica\AvaTax\Helper\Config              $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\ObjectManagerInterface  $objectManager
     * @param \Magento\Framework\Config\CacheInterface   $cache
     */
    public function __construct(
        \OnePica\AvaTax\Helper\Config $config,
        StoreManagerInterface $storeManager,
        ObjectManagerInterface $objectManager,
        \Magento\Framework\Config\CacheInterface $cache
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->objectManager = $objectManager;
        $this->cache = $cache;
    }

    /**
     * After get method plugin
     * 
     * If avatax disabled 'system-avatax16-disable.xml' config file will be merged with base system configuration
     *
     * @param \Magento\Config\Model\Config\Structure\Data $data
     * @param  array                                      $result
     * @return array
     */
    public function afterGet(\Magento\Config\Model\Config\Structure\Data $data, $result)
    {
        if (!$this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            /** @var \Magento\Config\Model\Config\Structure\Reader $reader */
            $config = $this->getAvatax16SystemDisableConfig();
            $result = array_replace_recursive($result, $config);
        }

        return $result;
    }

    /**
     * Get avatax16 system disable config
     *
     * @return array
     */
    protected function getAvatax16SystemDisableConfig()
    {
        $config = $this->cache->load(self::CACHE_KEY);

        if ($config) {
            $config = unserialize($config);

            return $config;
        }

        $reader = $this->objectManager->get('OnePica\AvaTax\Model\Config\Structure\Reader'); //virtualType
        $config = $reader->read('adminhtml');

        if (isset($config['config']['system'])) {
            $config = $config['config']['system'];
        }

        $this->cache->save(serialize($config), self::CACHE_KEY);

        return $config;
    }
}
