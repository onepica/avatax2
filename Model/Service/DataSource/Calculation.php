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
namespace OnePica\AvaTax\Model\Service\DataSource;

use Magento\Customer\Model\Customer;
use Magento\Framework\DataObject;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Store\Model\Store;
use Magento\Tax\Helper\Data;
use OnePica\AvaTax16\Document\Part\Location;

/**
 * Class DataSource
 *
 * @package OnePica\AvaTax\Model\Service
 */
class Calculation extends AbstractDataSource
{
    /**
     * Get Billing Address From Address
     *
     * @param Address $address
     * @return Address
     */
    protected function getBillingAddressFromAddress($address)
    {
        return $address->getQuote()->getBillingAddress();
    }

    /**
     * Get customer id from Address
     *
     * @param AddressInterface $address
     * @return int
     */
    protected function getCustomerIdFromAddress($address)
    {
        $customerId = (int)$address->getCustomerId();

        return $customerId;
    }

    /**
     * Get Customer Tax Class Id from Address
     *
     * @param Address $address
     * @return string
     */
    protected function getCustomerTaxClassIdFromAddress($address)
    {
        return $address->getQuote()->getCustomerTaxClassId();
    }

    /**
     * Get item avalara goods and services type
     * Tax class.
     *
     * @param QuoteItem|\Magento\Quote\Model\Quote\Address\Item $item
     * @param Store                                             $store
     *
     * @return string|null
     */
    public function getItemAvalaraGoodsAndServicesType($item, $store)
    {
        if ($item->getChildren()) {
            $item = $item->getChildren()[0];
        }

        return parent::getItemAvalaraGoodsAndServicesType($item, $store);
    }
}
