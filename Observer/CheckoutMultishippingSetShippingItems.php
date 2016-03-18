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
namespace OnePica\AvaTax\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\Phrase;
use OnePica\AvaTax\Helper\Config;

/**
 * Class CheckoutMultishippingSetShippingItems
 *
 * @package OnePica\AvaTax\Observer
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
     * CheckoutMultishippingSetShippingItems constructor
     *
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        ManagerInterface $messageManager
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
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
        $quote = $observer->getData('quote');
        $shippingAddresses = $quote->getAllShippingAddresses();
        $errors = array();
        foreach ($shippingAddresses as $address) {
            $itemResult = $address->validate();
            if (is_array($itemResult)) {
                // @todo refactor deprecated method
                $errors[] = sprintf($this->getValidateAddressMessage(), $address->format('oneline'));
            }
        }
        if (!empty($errors)) {
            $this->handleErrors($errors);
        } else {
            // save normalized data
            $quote->save();
        }
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
        throw new LocalizedException(new Phrase($errorsStr));
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
}
