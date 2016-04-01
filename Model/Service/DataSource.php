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
namespace OnePica\AvaTax\Model\Service;

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
use OnePica\AvaTax\Api\DataSourceInterface;
use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Model\GiftWrappingHelperFactory;
use OnePica\AvaTax\Model\Source\Avatax16\CustomerCodeFormat;
use OnePica\AvaTax16\Document\Part\Location;
use OnePica\AvaTax16\Document\Part\Location\Address as AvataxAddress;

/**
 * Class DataSource
 *
 * @package OnePica\AvaTax\Model\Service
 */
class DataSource implements DataSourceInterface
{
    /**#@+
     * Tax location purpose
     */
    const TAX_LOCATION_PURPOSE_SHIP_FROM = 'ShipFrom';
    const TAX_LOCATION_PURPOSE_SHIP_TO   = 'ShipTo';
    /**#@-*/

    /**
     * Config helper
     *
     * @var \OnePica\AvaTax\Helper\Config
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
     * @param \OnePica\AvaTax\Helper\Config                   $config
     * @param \Magento\Customer\Model\CustomerRegistry        $customerRegistry
     * @param \Magento\Tax\Model\ClassModelRegistry           $classModelRegistry
     * @param \Magento\Directory\Model\RegionFactory          $regionFactory
     * @param Data                                            $taxDataHelper
     * @param Collection                                      $taxClassCollection
     * @param \OnePica\AvaTax\Model\GiftWrappingHelperFactory $giftWrappingHelperFactory
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
        $customerId = (int)$address->getCustomerId();
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
        return (string)$address->getVatId() ?: (string)$address->getQuote()->getBillingAddress()->getVatId();
    }

    /**
     * Get default buyer type
     *
     * @param Address $address
     * @return string
     */
    public function getDefaultBuyerType($address)
    {
        return $this->getOpAvataxCode(
            $address->getQuote()->getCustomerTaxClassId()
        );
    }

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
        return $this->prepareNewAddressObject(
            $address->getStreetLine(1),
            $address->getStreetLine(2),
            $address->getCity(),
            $address->getRegion(),
            $address->getPostcode(),
            $address->getCountry()
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
        $customerId = (int)$address->getCustomerId();
        if (!$customerId) {
            $customerId = 'guest-' . $address->getId();
        }

        return $customerId;
    }

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
     * Get shipping tax class
     *
     * @param null|Store $store
     * @return string
     */
    public function getShippingTaxClass($store = null)
    {
        return $this->getOpAvataxCode($this->taxDataHelper->getShippingTaxClass($store));
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
