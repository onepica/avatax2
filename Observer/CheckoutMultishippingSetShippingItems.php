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

/**
 * Class CheckoutMultishippingSetShippingItems
 *
 * @package OnePica\AvaTax\Observer
 */
class CheckoutMultishippingSetShippingItems implements ObserverInterface
{
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
                $errors[] = $address->format('oneline');
            }
        }
        if (!empty($errors)) {
            $this->handleErrors($errors);
        }
    }

    /**
     * Handle Errors
     *
     * @param array $errors
     * @return void
     * @throws \Exception
     */
    protected function handleErrors($errors)
    {
        throw new \Exception('Validation error');
    }
}
