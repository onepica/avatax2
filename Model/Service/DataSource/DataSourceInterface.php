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

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Store\Model\Store;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;

/**
 * Class DataSource
 *
 * @package OnePica\AvaTax\Model\Service
 */
interface DataSourceInterface
{
    /**
     * Get customer code
     *
     * @param Store            $store
     * @param AddressInterface $address
     * @return string
     */
    public function getCustomerCode($store, $address);

    /**
     * Get vat Id
     *
     * @param Store            $store
     * @param AddressInterface $address
     * @return string
     */
    public function getTaxBuyerCode($store, $address);

    /**
     * Get default buyer type
     *
     * @param AddressInterface $address
     * @return string
     */
    public function getDefaultBuyerType($address);

    /**
     * Get default location
     *
     * @param Store            $store
     * @param AddressInterface $address
     * @return array
     */
    public function getDefaultLocations($store, $address);

    /**
     * Get shipping tax class
     *
     * @param null|Store $store
     * @return string
     */
    public function getShippingTaxClass($store = null);

    /**
     * Tax included
     *
     * @param null|Store $store
     * @return bool
     */
    public function taxIncluded($store = null);

    /**
     * Apply tax after discount
     *
     * @param null|Store $store
     * @return mixed
     */
    public function applyTaxAfterDiscount($store = null);

    /**
     * Is discounted
     *
     * @param QuoteItem|InvoiceItem|CreditmemoItem $item
     * @param Store                                $store
     * @return string 'true' or 'false'
     */
    public function isDiscounted($item, $store);

    /**
     * Get item code
     *
     * @param QuoteItem|InvoiceItem|CreditmemoItem $item
     * @param Store                                $store
     * @return string
     */
    public function getItemCode($item, $store);

    /**
     * Get item avalara goods and services type
     *
     * Tax class
     *
     * @param QuoteItem|InvoiceItem|CreditmemoItem $item
     * @param Store                                $store
     * @return string|null
     */
    public function getItemAvalaraGoodsAndServicesType($item, $store);

    /**
     * Get item meta data
     *
     * @param QuoteItem|InvoiceItem|CreditmemoItem $item
     * @param Store                                $store
     * @return array
     */
    public function getItemMetaData($item, $store);

    /**
     * Get gw item avalara goods and services type
     *
     * Tax class
     *
     * @param Store                                $store
     * @return string|null
     */
    public function getGwItemAvalaraGoodsAndServicesType($store);
}
