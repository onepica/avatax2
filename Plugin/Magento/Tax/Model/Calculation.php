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
namespace Astound\AvaTax\Plugin\Magento\Tax\Model;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Calculation
 *
 * @package Astound\AvaTax\Plugin\Magento\Tax\Model
 */
class Calculation
{
    /**
     * Config
     *
     * @var \Astound\AvaTax\Helper\Config
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
     * @param \Astound\AvaTax\Helper\Config              $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Astound\AvaTax\Helper\Config $config, StoreManagerInterface $storeManager)
    {
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * Get rates method plugin
     *
     * @param \Magento\Tax\Model\Calculation $calculation
     * @param  array                         $result
     * @return array
     */
    public function afterGetRates(\Magento\Tax\Model\Calculation $calculation, $result)
    {
        if (!$this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            return $result;
        }

        return [];
    }

    /**
     * Get rate method plugin
     *
     * @param \Magento\Tax\Model\Calculation $calculation
     * @param  float                         $result
     * @return int
     */
    public function afterGetRate(\Magento\Tax\Model\Calculation $calculation, $result)
    {
        if (!$this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            return $result;
        }

        return 0;
    }

    /**
     * Get store rate method plugin
     *
     * @param \Magento\Tax\Model\Calculation $calculation
     * @param  float                         $result
     * @return int
     */
    public function afterGetStoreRate(\Magento\Tax\Model\Calculation $calculation, $result)
    {
        if (!$this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            return $result;
        }

        return 0;
    }

    /**
     * Get applied rates method plugin
     *
     * @param \Magento\Tax\Model\Calculation $calculation
     * @param  array                         $result
     * @return array
     */
    public function afterGetAppliedRates(\Magento\Tax\Model\Calculation $calculation, $result)
    {
        if (!$this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            return $result;
        }

        return [];
    }

    /**
     * Get tax rates method plugin
     *
     * @param \Magento\Tax\Model\Calculation $calculation
     * @param  array                         $result
     * @return array
     */
    public function afterGetTaxRates(\Magento\Tax\Model\Calculation $calculation, $result)
    {
        if (!$this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            return $result;
        }

        return [];
    }
}
