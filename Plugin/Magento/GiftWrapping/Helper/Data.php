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
namespace OnePica\AvaTax\Plugin\Magento\GiftWrapping\Helper;

use Magento\Store\Model\StoreManagerInterface;
use OnePica\AvaTax\Helper\Config as AvataxConfigHelper;
use Magento\Tax\Model\Config;

/**
 * Class Data
 *
 * @package OnePica\AvaTax\Plugin\Magento\GiftWrapping\Helper
 */
class Data
{
    /**
     * Config
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
     * Tax config
     *
     * @var \Magento\Tax\Model\Config
     */
    protected $taxConfig;

    /**
     * Constructor
     *
     * @param AvataxConfigHelper                         $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Tax\Model\Config                  $taxConfig
     */
    public function __construct(
        AvataxConfigHelper $config,
        StoreManagerInterface $storeManager,
        Config $taxConfig
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->taxConfig = $taxConfig;
    }

    /**
     * Display gift wrapping prices incl tax method plugin
     *
     * @param \Magento\GiftWrapping\Helper\Data $data
     * @param  bool                             $result
     * @return bool
     */
    public function afterDisplayCartWrappingIncludeTaxPrice(\Magento\GiftWrapping\Helper\Data $data, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            if ($this->taxConfig->priceIncludesTax($this->storeManager->getStore())) {
                return true;
            }

            return false;
        }

        return $result;
    }

    /**
     * Display gift wrapping prices excl tax method plugin
     *
     * @param \Magento\GiftWrapping\Helper\Data $data
     * @param  bool                             $result
     * @return bool
     */
    public function afterDisplayCartWrappingExcludeTaxPrice(\Magento\GiftWrapping\Helper\Data $data, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            if (!$this->taxConfig->priceIncludesTax($this->storeManager->getStore())) {
                return true;
            }

            return false;
        }

        return $result;
    }

    /**
     * Display gift wrapping prices both tax method plugin
     *
     * @param \Magento\GiftWrapping\Helper\Data $data
     * @param  bool                             $result
     * @return bool
     */
    public function afterDisplayCartWrappingBothPrices(\Magento\GiftWrapping\Helper\Data $data, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            return false;
        }

        return $result;
    }

    /**
     * Display cart card prices excl tax method plugin
     *
     * @param \Magento\GiftWrapping\Helper\Data $data
     * @param  bool                             $result
     * @return bool
     */
    public function afterDisplayCartCardIncludeTaxPrice(\Magento\GiftWrapping\Helper\Data $data, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            if ($this->taxConfig->priceIncludesTax($this->storeManager->getStore())) {
                return true;
            }

            return false;
        }

        return $result;
    }

    /**
     * Display cart card prices both tax method plugin
     *
     * @param \Magento\GiftWrapping\Helper\Data $data
     * @param  bool                             $result
     * @return bool
     */
    public function afterDisplayCartCardBothPrices(\Magento\GiftWrapping\Helper\Data $data, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            return false;
        }

        return $result;
    }
}
