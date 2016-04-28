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
namespace OnePica\AvaTax\Plugin\Magento\Weee\Model;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Weee\Model\Tax as WeeeModelTax;

/**
 * Class Config
 *
 * @package OnePica\AvaTax\Plugin\Magento\Weee\Model
 */
class Config
{
    /**
     * Avatax config helper
     *
     * @var \OnePica\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * Store manager
     * 
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Config constructor.
     *
     * @param \OnePica\AvaTax\Helper\Config $config
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(\OnePica\AvaTax\Helper\Config $config, StoreManagerInterface $storeManager)
    {
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * After isEnable method plugin
     *
     * @param \Magento\Weee\Model\Config $config
     * @param $result
     * @return bool
     */
    public function afterIsEnabled(\Magento\Weee\Model\Config $config, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            return true;
        }

        return $result;
    }

    /**
     * After isTaxable method plugin
     *
     * @param \Magento\Weee\Model\Config $config
     * @param $result
     * @return bool
     */
    public function afterIsTaxable(\Magento\Weee\Model\Config $config, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            return false;
        }

        return $result;
    }

    /**
     * After getListPriceDisplayType method plugin
     *
     * @param \Magento\Weee\Model\Config $config
     * @param int $result
     * @return int
     */
    public function afterGetListPriceDisplayType(\Magento\Weee\Model\Config $config, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            return WeeeModelTax::DISPLAY_EXCL;
        }

        return $result;
    }

    /**
     * After getPriceDisplayType method plugin
     *
     * @param \Magento\Weee\Model\Config $config
     * @param int $result
     * @return int
     */
    public function afterGetPriceDisplayType(\Magento\Weee\Model\Config $config, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            return WeeeModelTax::DISPLAY_EXCL;
        }

        return $result;
    }
}