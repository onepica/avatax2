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
namespace Astound\AvaTax\Model\Service\DataSource;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Store\Model\Store;
use Magento\Tax\Helper\Data;
use Magento\Tax\Model\ClassModelRegistry;
use Magento\Tax\Model\ResourceModel\TaxClass\Collection;
use Astound\AvaTax\Helper\Config;
use Astound\AvaTax\Model\GiftWrappingHelperFactory;
use Astound\AvaTax\Model\Source\Avatax16\CustomerCodeFormat;
use OnePica\AvaTax16\Document\Part\Location;
use OnePica\AvaTax16\Document\Part\Location\Address as AvataxAddress;

abstract class AbstractDataSource implements DataSourceInterface
{
    /**
     * Default shipping tax class
     */
    const DEFAULT_SHIPPING_TAX_CLASS = 'FR020100';

    /**#@+
     * Tax location purpose
     */
    const TAX_LOCATION_PURPOSE_SHIP_FROM = 'ShipFrom';
    const TAX_LOCATION_PURPOSE_SHIP_TO   = 'ShipTo';
    /**#@-*/

    /**
     * Config helper
     *
     * @var \Astound\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * Customer registry
     *
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * Tax class model registry
     *
     * @var \Magento\Tax\Model\ClassModelRegistry
     */
    protected $classModelRegistry;

    /**
     * Region factory
     *
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;

    /**
     * Tax data helper
     *
     * @var Data
     */
    protected $taxDataHelper;

    /**
     * Avatax data
     *
     * @var DataObject
     */
    protected $avataxData;

    /**
     * Tax class collection
     *
     * @var Collection
     */
    protected $taxClassCollection;

    /**
     * Giftwrapping data helper
     *
     * @var \Magento\GiftWrapping\Helper\Data|null
     */
    protected $giftWrappingHelper;

    /**
     * DataSource constructor.
     *
     * @param \Astound\AvaTax\Helper\Config                   $config
     * @param \Magento\Customer\Model\CustomerRegistry        $customerRegistry
     * @param \Magento\Tax\Model\ClassModelRegistry           $classModelRegistry
     * @param \Magento\Directory\Model\RegionFactory          $regionFactory
     * @param Data                                            $taxDataHelper
     * @param Collection                                      $taxClassCollection
     * @param \Astound\AvaTax\Model\GiftWrappingHelperFactory $giftWrappingHelperFactory
     */
    public function __construct(
        Config $config,
        CustomerRegistry $customerRegistry,
        ClassModelRegistry $classModelRegistry,
        RegionFactory $regionFactory,
        Data $taxDataHelper,
        Collection $taxClassCollection,
        GiftWrappingHelperFactory $giftWrappingHelperFactory
    ) {
        $this->config = $config;
        $this->customerRegistry = $customerRegistry;
        $this->classModelRegistry = $classModelRegistry;
        $this->regionFactory = $regionFactory;
        $this->taxDataHelper = $taxDataHelper;
        $this->taxClassCollection = $taxClassCollection;
        $this->giftWrappingHelper = $giftWrappingHelperFactory->create();
    }

    /**
     * Init data
     *
     * @param array| null $items
     * @param Store       $store
     * @return $this
     */
    public function initAvataxData($items, $store)
    {
        if (empty($items)) {
            return $this;
        }

        $this->avataxData = new DataObject();
        $taxClassIds = [];
        /** @var QuoteItem $item */
        foreach ($items as $item) {
            $this->avataxData->setData($item->getId(), unserialize($item->getData('avatax_data')));
            $taxClassIds[] = (int)$this->avataxData->getData($item->getId() . '/tax_class_id');
        }

        $taxClassIds[] = $this->giftWrappingHelper
            ? (int)$this->giftWrappingHelper->getWrappingTaxClass($store)
            : null;

        $this->taxClassCollection->addFieldToFilter('class_id', ['in' => array_unique($taxClassIds)]);

        return $this;
    }

    /**
     * Get customer code
     *
     * @param Store   $store
     * @param Address $address
     * @return string
     */
    public function getCustomerCode($store, $address)
    {
        $customerId = $this->getCustomerIdFromAddress($address);
        $customer = null;

        if ($customerId) {
            try {
                $customer = $this->customerRegistry->retrieve($customerId);
            } catch (NoSuchEntityException $e) {
                $customer = null;
            }
        }

        $customerCode = '';
        switch ($this->config->getCustomerCodeFormat($store)) {
            case CustomerCodeFormat::CUSTOMER_ID:
                $customerCode = $this->prepareCustomerId($address);
                break;
            case CustomerCodeFormat::CUSTOMER_EMAIL:
                $customerCode = $this->prepareCustomerEmail($address, $customer)
                    ?: $this->prepareCustomerId($address);
                break;
        }

        return $customerCode;
    }

    /**
     * Get vat Id
     *
     * @param Store   $store
     * @param Address $address
     * @return string
     */
    public function getTaxBuyerCode($store, $address)
    {
        return (string)$address->getVatId();
    }

    /**
     * Get Billing Address From Address
     *
     * @param Address $address
     * @return Address
     */
    abstract protected function getBillingAddressFromAddress($address);

    /**
     * Get default buyer type
     *
     * @param Address $address
     * @return string
     */
    public function getDefaultBuyerType($address)
    {
        return $this->getOpAvataxCode(
            $this->getCustomerTaxClassIdFromAddress($address)
        );
    }

    /**
     * Get Customer Tax Class Id from Address
     *
     * @param Address $address
     * @return string
     */
    abstract protected function getCustomerTaxClassIdFromAddress($address);

    /**
     * Get default location
     *
     * @param Store   $store
     * @param Address $address
     * @return array
     */
    public function getDefaultLocations($store, $address)
    {
        $locationFrom = new Location();
        $locationFrom->setTaxLocationPurpose(self::TAX_LOCATION_PURPOSE_SHIP_FROM);
        $locationFrom->setAddress($this->getOriginalAddress($store));

        $locationTo = new Location();
        $locationTo->setTaxLocationPurpose(self::TAX_LOCATION_PURPOSE_SHIP_TO);
        $locationTo->setAddress($this->getDestinationAddress($address));

        $defaultLocations = [
            self::TAX_LOCATION_PURPOSE_SHIP_FROM => $locationFrom,
            self::TAX_LOCATION_PURPOSE_SHIP_TO   => $locationTo
        ];

        return $defaultLocations;
    }

    /**
     * Get original address object
     *
     * @param Store $store
     * @return \OnePica\AvaTax16\Document\Part\Location\Address
     */
    protected function getOriginalAddress($store)
    {
        return $this->prepareNewAddressObject(
            $this->config->getOriginFirstStreetLine($store),
            $this->config->getOriginSecondStreetLine($store),
            $this->config->getOriginCity($store),
            $this->getRegionCode($this->config->getOriginRegionId($store)),
            $this->config->getOriginZip($store),
            $this->config->getOriginCountryId($store)
        );
    }

    /**
     * Get destination address object
     *
     * @param Address $address
     * @return \OnePica\AvaTax16\Document\Part\Location\Address
     */
    protected function getDestinationAddress($address)
    {
        $country = $address->getCountry() ?: $address->getCountryId();
        return $this->prepareNewAddressObject(
            $address->getStreetLine(1),
            $address->getStreetLine(2),
            $address->getCity(),
            $address->getRegion(),
            $address->getPostcode(),
            $country
        );
    }

    /**
     * Prepare new avatax address object
     *
     * @param string $line1
     * @param string $line2
     * @param string $city
     * @param string $state
     * @param string $zip
     * @param string $country
     * @return \OnePica\AvaTax16\Document\Part\Location\Address
     */
    protected function prepareNewAddressObject($line1, $line2, $city, $state, $zip, $country = 'USA')
    {
        $address = new AvataxAddress();
        $line1 = $line1 ?: '_';
        $address->setLine1((string)$line1);
        $address->setLine2((string)$line2);
        $address->setCity((string)$city);
        $address->setState((string)$state);
        $address->setZipcode((string)$zip);
        $address->setCountry((string)$country);

        return $address;
    }

    /**
     * Prepare customer id
     *
     * @param AddressInterface $address
     * @return int|string
     */
    protected function prepareCustomerId($address)
    {
        $customerId = $this->getCustomerIdFromAddress($address);
        if (!$customerId) {
            $customerId = 'guest-' . $address->getId();
        }

        return $customerId;
    }

    /**
     * Get customer id from Address
     *
     * @param AddressInterface $address
     * @return int
     */
    abstract protected function getCustomerIdFromAddress($address);

    /**
     * Prepare customer email
     *
     * @param Address       $address
     * @param Customer|null $customer
     * @return string
     */
    public function prepareCustomerEmail($address, $customer)
    {
        $email = '';

        if ($address->getEmail()) {
            $email = $address->getEmail();
        }

        if (!$email) {
            $email = $address->getQuote()->getBillingAddress()->getEmail();
        }

        if (!$email && $customer) {
            $email = $customer->getEmail();
        }

        return $email;
    }

    /**
     * Get region code by region id
     *
     * @param int $regionId
     * @return string
     */
    protected function getRegionCode($regionId)
    {
        return (string)$this->regionFactory->create()->load((int)$regionId)->getCode();
    }

    /**
     * Returns AvaTax's hard-coded shipping tax class
     *
     * @param null|Store $store
     * @return string
     */
    public function getShippingTaxClass($store = null)
    {
        $shippingTaxClass = $this->getOpAvataxCode($this->taxDataHelper->getShippingTaxClass($store));

        if ($shippingTaxClass === '') {
            $shippingTaxClass = self::DEFAULT_SHIPPING_TAX_CLASS;
        }

        return $shippingTaxClass;
    }

    /**
     * Get OpAvataxCode by tax class id
     *
     * @param int $taxClassId
     * @return string
     */
    protected function getOpAvataxCode($taxClassId)
    {
        try {
            return (string)$this->classModelRegistry->retrieve(
                (int)$taxClassId
            )->getOpAvataxCode();
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }

    /**
     * Is tax included
     *
     * @param null|Store $store
     * @return bool
     */
    public function taxIncluded($store = null)
    {
        return (bool)$this->taxDataHelper->priceIncludesTax($store);
    }

    /**
     * Apply tax after discount
     *
     * @param null|Store $store
     * @return mixed
     */
    public function applyTaxAfterDiscount($store = null)
    {
        return $this->taxDataHelper->applyTaxAfterDiscount($store);
    }

    /**
     * @param QuoteItem|InvoiceItem|CreditmemoItem $item
     * @param Store                                $store
     * @return string 'true' or 'false'
     */
    public function isDiscounted($item, $store)
    {
        return ($this->taxDataHelper->applyTaxAfterDiscount($store) && (float)$item->getBaseDiscountAmount())
            ? 'true'
            : 'false';
    }

    /**
     * Get item code
     *
     * @param QuoteItem|InvoiceItem|CreditmemoItem $item
     * @param Store                                $store
     * @return string
     */
    public function getItemCode($item, $store)
    {
        $itemCode = '';

        if ($this->config->getUseUpcCode($store)) {
            $itemCode = (string)$this->avataxData->getData($item->getId() . '/upc_code');
            $itemCode = !empty($itemCode) ? 'UPC:' . $itemCode : '';
        }

        if (empty($itemCode)) {
            $itemCode = $item->getSku();
        }

        return substr($itemCode, 0, 50);
    }

    /**
     * Get item avalara goods and services type
     * Tax class.
     *
     * @param QuoteItem|InvoiceItem|CreditmemoItem $item
     * @param Store                                $store
     * @return string|null
     */
    public function getItemAvalaraGoodsAndServicesType($item, $store)
    {
        $taxClassId = $this->avataxData->getData($item->getId() . '/tax_class_id');
        $taxClassItem = $this->taxClassCollection->getItemById($taxClassId);

        return $taxClassItem ? $taxClassItem->getData('op_avatax_code') : '';
    }

    /**
     * Get item meta data
     *
     * @param QuoteItem|InvoiceItem|CreditmemoItem $item
     * @param Store                                $store
     * @return array
     */
    public function getItemMetaData($item, $store)
    {
        $id = $item->getChildren() ? $item->getChildren()[0]->getId() : $item->getId();
        $metaData = [
            'ref1' => $this->avataxData->getData($id . '/first_reference_code'),
            'ref2' => $this->avataxData->getData($id . '/second_reference_code'),
        ];

        return $metaData;
    }

    /**
     * Get gw item avalara goods and services type
     * Tax class
     *
     * @param Store $store
     * @return string|null
     */
    public function getGwItemAvalaraGoodsAndServicesType($store)
    {
        $taxClassId = $this->giftWrappingHelper
            ? $this->giftWrappingHelper->getWrappingTaxClass($store)
            : null;
        $taxClassItem = $this->taxClassCollection->getItemById($taxClassId);

        return $taxClassItem ? $taxClassItem->getData('op_avatax_code') : '';
    }
}