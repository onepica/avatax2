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
namespace Astound\AvaTax\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\RequestInterface;
use Astound\AvaTax\Helper\Config;
use Astound\AvaTax\Helper\Address as AvataxAddressHelper;

/**
 * Class CheckoutMultishippingSetShippingItems
 *
 * @package Astound\AvaTax\Observer
 */
class CheckoutMultishippingSetShippingItems implements ObserverInterface
{
    /**
     * @var Config
     */
    protected $config = null;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Message manager object
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Address helper
     *
     * @var AvataxAddressHelper
     */
    protected $addressHelper;

    /**
     * CheckoutMultishippingSetShippingItems constructor
     *
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param AvataxAddressHelper $addressHelper
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        ManagerInterface $messageManager,
        RequestInterface $request,
        AvataxAddressHelper $addressHelper
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->addressHelper = $addressHelper;
    }

    /**
     * Execute
     * Shipping addresses validation
     *
     * @param Observer $observer
     * @return void
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        if (!$this->canValidateAddresses()) {
            return $this;
        }

        $quote = $observer->getData('quote');
        $shippingAddresses = $quote->getAllShippingAddresses();
        $errors = array();
        $normalized = false;
        foreach ($shippingAddresses as $address) {
            $itemResult = $address->validate();
            if (is_array($itemResult)) {
                // @todo refactor deprecated method
                $errors[] = sprintf($this->getValidateAddressMessage(), $address->format('oneline'));
            }

            if ($address->getData('is_normalized')) {
                $normalized = true;
            }
        }
        if (!empty($errors)) {
            $this->handleErrors($errors);
        } else {
            // save normalized data
            $quote->save();
            if ($normalized) {
                // add normalize message
                $message = $this->getMultiaddressNormalizeMessage();
                $this->addressHelper->addValidationNotice($message);
            }
        }
    }

    /**
     * Can validate addresses
     *
     * @return bool
     */
    protected function canValidateAddresses()
    {
        // enable validation if going to next step of multishipping checkout
        if ($this->request->getParam('continue')) {
            return true;
        }

        return false;
    }

    /**
     * Handle Errors
     *
     * @param array $errors
     * @throws \Exception
     */
    protected function handleErrors($errors)
    {
        $errorsStr = implode('<br />', $errors);
        // stop go to next step of checkout
        throw new LocalizedException(__($errorsStr));
    }

    /**
     * Get Validate Address Message
     *
     * @return int
     */
    protected function getValidateAddressMessage()
    {
        return $this->config->getValidateAddressMessage($this->storeManager->getStore());
    }

    /**
     * Get Multiaddress Normalize Message
     *
     * @return int
     */
    protected function getMultiaddressNormalizeMessage()
    {
        return $this->config->getMultiaddressNormalizeMessage($this->storeManager->getStore());
    }
}
