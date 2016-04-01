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
namespace OnePica\AvaTax\Observer\Adminhtml;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use OnePica\AvaTax\Model\Source\Avatax16\Error;
use OnePica\AvaTax\Helper\Config;

/**
 * Class PreventOrderCreation
 *
 * @package OnePica\AvaTax\Observer\Adminhtml
 */
class PreventOrderCreation implements ObserverInterface
{
    /**
     * Config helper
     *
     * @var \OnePica\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * CheckoutSubmitBefore constructor.
     *
     * @param \OnePica\AvaTax\Helper\Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
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
        $quoteHasError = (bool)$quote->getData('avatax_error');

        if ($this->config->isAvaTaxEnabled($store)
            && $quoteHasError
            && $this->config->getActionOnError($store) === Error::DISABLE_CHECKOUT
        ) {
            throw new LocalizedException(__($this->config->getBackendErrorMessage($store)));
        }
    }
}
