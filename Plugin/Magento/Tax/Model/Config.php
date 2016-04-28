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
namespace OnePica\AvaTax\Plugin\Magento\Tax\Model;

use Magento\Store\Model\StoreManagerInterface;
use OnePica\AvaTax\Helper\Config as AvataxConfigHelper;

/**
 * Class Config
 *
 * @package OnePica\AvaTax\Plugin\Magento\Tax\Model
 */
class Config
{
    /**
     * Avatax shipping tax class
     */
    const AVATAX_SHIPPING_TAX_CLASS = 'FR020100';

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
     * Calculation constructor.
     *
     * @param AvataxConfigHelper                         $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(AvataxConfigHelper $config, StoreManagerInterface $storeManager)
    {
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * Apply tax after discount method plugin
     *
     * @param \Magento\Tax\Model\Config $config
     * @param                           $result
     * @return mixed
     */
    public function afterApplyTaxAfterDiscount(\Magento\Tax\Model\Config $config, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            if (!$config->priceIncludesTax($this->storeManager->getStore())) {
                return true;
            }
        }

        return $result;
    }

    /**
     * Discount tax method plugin
     *
     * @param \Magento\Tax\Model\Config $config
     * @param                           $result
     * @return mixed
     */
    public function afterDiscountTax(\Magento\Tax\Model\Config $config, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            if ($config->priceIncludesTax($this->storeManager->getStore())) {
                return true;
            }

            return false;
        }

        return $result;
    }

    /**
     * Shipping price include tax method plugin
     *
     * @param \Magento\Tax\Model\Config $config
     * @param                           $result
     * @return mixed
     */
    public function afterShippingPriceIncludesTax(\Magento\Tax\Model\Config $config, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            if ($config->priceIncludesTax($this->storeManager->getStore())) {
                return true;
            }

            return false;
        }

        return $result;
    }

    /**
     * Cross border trade enabled method plugin
     *
     * @param \Magento\Tax\Model\Config $config
     * @param                           $result
     * @return mixed
     */
    public function afterCrossBorderTradeEnabled(\Magento\Tax\Model\Config $config, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            if ($config->priceIncludesTax($this->storeManager->getStore())) {
                return true;
            }

            return false;
        }

        return $result;
    }

    /**
     * Get perice display type method plugin
     *
     * @param \Magento\Tax\Model\Config $config
     * @param                           $result
     * @return mixed
     */
    public function afterGetPriceDisplayType(\Magento\Tax\Model\Config $config, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            if ($config->priceIncludesTax($this->storeManager->getStore())) {
                return \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX;
            }

            return \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX;
        }

        return $result;
    }


    /**
     * Get shipping price display type method plugin
     *
     * @param \Magento\Tax\Model\Config $config
     * @param                           $result
     * @return mixed
     */
    public function afterGetShippingPriceDisplayType(\Magento\Tax\Model\Config $config, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            if ($config->shippingPriceIncludesTax($this->storeManager->getStore())) {
                return \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX;
            }

            return \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX;
        }

        return $result;
    }

    /**
     * Display cart prices incl tax method plugin
     *
     * @param \Magento\Tax\Model\Config $config
     * @param  bool                     $result
     * @return bool
     */
    public function afterDisplayCartPricesInclTax(\Magento\Tax\Model\Config $config, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            if ($config->priceIncludesTax($this->storeManager->getStore())) {
                return true;
            }

            return false;
        }

        return $result;
    }

    /**
     * Display cart prices exl tax method plugin
     *
     * @param \Magento\Tax\Model\Config $config
     * @param  bool                     $result
     * @return bool
     */
    public function afterDisplayCartPricesExclTax(\Magento\Tax\Model\Config $config, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            if (!$config->priceIncludesTax($this->storeManager->getStore())) {
                return true;
            }

            return false;
        }

        return $result;
    }

    /**
     * Display cart prices both method plugin
     *
     * @param \Magento\Tax\Model\Config $config
     * @param  bool                     $result
     * @return bool
     */
    public function afterDisplayCartPricesBoth(\Magento\Tax\Model\Config $config, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            return false;
        }

        return $result;
    }

    /**
     * Display cart subtotal include tax method plugin
     *
     * @param \Magento\Tax\Model\Config $config
     * @param  bool                     $result
     * @return bool
     */
    public function afterDisplayCartSubtotalInclTax(\Magento\Tax\Model\Config $config, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            if ($config->priceIncludesTax($this->storeManager->getStore())) {
                return true;
            }

            return false;
        }

        return $result;
    }

    /**
     * Display cart subtotal exclude tax method plugin
     *
     * @param \Magento\Tax\Model\Config $config
     * @param  bool                     $result
     * @return bool
     */
    public function afterDisplayCartSubtotalExclTax(\Magento\Tax\Model\Config $config, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            if (!$config->priceIncludesTax($this->storeManager->getStore())) {
                return true;
            }

            return false;
        }

        return $result;
    }

    /**
     * Display cart subtotal both method plugin
     *
     * @param \Magento\Tax\Model\Config $config
     * @param  bool                     $result
     * @return bool
     */
    public function afterDisplayCartSubtotalBoth(\Magento\Tax\Model\Config $config, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            return false;
        }

        return $result;
    }
}
