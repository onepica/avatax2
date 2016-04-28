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
namespace Astound\AvaTax\Plugin\Magento\Quote\Model\Quote;

use Magento\Store\Model\StoreManagerInterface;
use Astound\AvaTax\Helper\Config as ConfigHelper;

/**
 * Class Config
 *
 * @package Astound\AvaTax\Plugin\Magento\Quote\Model\Quote
 */
class Config
{
    /**
     * Config helper
     *
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Config constructor.
     *
     * @param ConfigHelper          $configHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(ConfigHelper $configHelper, StoreManagerInterface $storeManager)
    {
        $this->configHelper = $configHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * After getProductAttributes plugin
     *
     * @param \Magento\Quote\Model\Quote\Config $configObject
     * @param array                             $result
     * @return array
     */
    public function afterGetProductAttributes(\Magento\Quote\Model\Quote\Config $configObject, $result)
    {
        return array_merge($result, $this->getAvataxAttributesCode());
    }

    /**
     * Get avatax attribute codes
     *
     * @return array
     */
    protected function getAvataxAttributesCode()
    {
        $store = $this->storeManager->getStore();
        $attributesCode = [];

        $attributesCode[] = $this->configHelper->getFirstReferenceCode($store);
        $attributesCode[] = $this->configHelper->getSecondReferenceCode($store);


        if ($this->configHelper->getUseUpcCode($store)) {
            $attributesCode[] = $this->configHelper->getUpcCode($store);
        }

        return $attributesCode;
    }
}
