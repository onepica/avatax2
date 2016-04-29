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
 * @author     Astound Codemaster <codemaster@astoundcommerce.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Plugin\Magento\Tax\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Astound\AvaTax\Helper\Config as AvataxConfigHelper;

/**
 * Class Data
 *
 * @package Astound\AvaTax\Plugin\Magento\Tax\Helper
 */
class Data
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
     * @param AvataxConfigHelper                         $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(AvataxConfigHelper $config, StoreManagerInterface $storeManager)
    {
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * AvaTax always computes tax based on ship from and ship to addresses
     *
     * @param \Magento\Tax\Helper\Data $data
     * @param  string|null             $result
     * @return string
     */
    public function afterGetTaxBasedOn(\Magento\Tax\Helper\Data $data, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            return 'shipping';
        }

        return $result;
    }

    /**
     * Always apply tax on custom price
     *
     * @param \Magento\Tax\Helper\Data $data
     * @param  bool                    $result
     * @return bool
     */
    public function afterApplyTaxOnCustomPrice(\Magento\Tax\Helper\Data $data, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            return true;
        }

        return $result;
    }

    /**
     * Always apply tax on custom price (not original)
     *
     * @param \Magento\Tax\Helper\Data $data
     * @param  bool                    $result
     * @return bool
     */
    public function afterApplyTaxOnOriginalPrice(\Magento\Tax\Helper\Data $data, $result)
    {
        if ($this->config->isAvaTaxEnabled($this->storeManager->getStore())) {
            return false;
        }

        return $result;
    }
}
