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
namespace OnePica\AvaTax\Plugin\Magento\Quote\Model\Quote;

/**
 * Class Config
 *
 * @package OnePica\AvaTax\Plugin\Magento\Quote\Model\Quote
 */
use Magento\Store\Model\StoreManagerInterface;
use OnePica\AvaTax\Helper\Config as ConfigHelper;

/**
 * Class Config
 *
 * @package OnePica\AvaTax\Plugin\Magento\Quote\Model\Quote
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
