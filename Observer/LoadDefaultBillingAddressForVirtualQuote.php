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
namespace Astound\AvaTax\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Astound\AvaTax\Helper\Config;
use Astound\AvaTax\Model\Source\Avatax16\Action as AvataxActionSource;

/**
 * Class LoadDefaultBillingAddressForVirtualQuote
 *
 * @package Astound\AvaTax\Observer
 */
class LoadDefaultBillingAddressForVirtualQuote implements ObserverInterface
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var \Astound\AvaTax\Helper\Config
     */
    protected $config;


    /**
     * Constructor
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        Config $config

    ) {
        $this->customerSession = $customerSession;
        $this->addressRepository = $addressRepository;
        $this->config = $config;
    }

    /**
     * Execute
     * Calculate tax in case virtual quote with default billing address on checkout index page
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->config->getServiceAction() != AvataxActionSource::ACTION_DISABLE) {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $observer->getData('controller_action')->getOnepage()->getQuote();
            if ($quote->isVirtual()) {
                $address = $quote->getBillingAddress();
                $billingAddressId = $this->customerSession->getCustomer()->getDefaultBilling();
                if (!$address->getPostcode() && $billingAddressId) {
                    $addressData = $this->addressRepository->getById($billingAddressId);
                    $address->importCustomerAddressData($addressData);
                }
                $quote->collectTotals();
            }
        }
    }
}
