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
namespace Astound\AvaTax\Observer\Adminhtml;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Astound\AvaTax\Helper\Config;
use Astound\AvaTax\Helper\Address as AvataxAddressHelper;

/**
 * Class AddAddressNormalizationNotice
 *
 * @package Astound\AvaTax\Observer\Adminhtml
 */
class AddAddressNormalizationNotice implements ObserverInterface
{
    /**
     * Config helper
     *
     * @var \Astound\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * Address helper
     *
     * @var AvataxAddressHelper
     */
    protected $addressHelper;

    /**
     * Constructor.
     *
     * @param \Astound\AvaTax\Helper\Config $config
     * @param AvataxAddressHelper $addressHelper
     */
    public function __construct(
        Config $config,
        AvataxAddressHelper $addressHelper
    ) {
        $this->config = $config;
        $this->addressHelper = $addressHelper;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getData('quote');
        $store = $quote->getStore();
        $shippingAddress = $quote->getShippingAddress();
        $addressNormalized = ($shippingAddress && $shippingAddress->getData('is_normalized')) ? true : false;
        if ($addressNormalized) {
            $message = $this->config->getMultiaddressNormalizeMessage($store);
            $this->addressHelper->addValidationNotice($message);
        }
    }
}
