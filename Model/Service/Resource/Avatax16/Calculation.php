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
namespace Astound\AvaTax\Model\Service\Resource\Avatax16;

use DateTime;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Store\Model\Store;
use Astound\AvaTax\Model\Service\ConfigRepositoryInterface;
use Astound\AvaTax\Api\Service\LoggerInterface;
use Astound\AvaTax\Helper\Config;
use Astound\AvaTax\Model\Service\DataSource\Calculation as CalculationDataSource;
use Astound\AvaTax\Model\Service\Resource\AbstractResource;
use Astound\AvaTax\Model\Service\Result\Storage\Calculation as CalculationResultStorage;
use OnePica\AvaTax16\Document\Request;
use OnePica\AvaTax16\Document\Response\Line;

/**
 * Class Calculation
 *
 * @method \Astound\AvaTax\Model\Service\Result\Calculation send(Store $store)
 * @package Astound\AvaTax\Model\Service\Resource\Avatax
 */
class Calculation extends AbstractResource
{
    /**
     * Timezone
     *
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $timezone;

    /**
     * Stop sending request flag
     *
     * @var bool
     */
    protected $stopRequest = false;

    /**
     * Calculation result storage
     *
     * @var CalculationResultStorage
     */
    protected $resultStorage;

    /**
     * Item-Gift pair
     *
     * @var array
     */
    protected $itemGiftPair = [];

    /**
     * Calculation constructor.
     *
     * @param \Astound\AvaTax\Model\Service\ConfigRepositoryInterface $configRepository
     * @param \Magento\Framework\ObjectManagerInterface               $objectManager
     * @param \Astound\AvaTax\Helper\Config                           $config
     * @param \Astound\AvaTax\Api\Service\LoggerInterface             $logger
     * @param CalculationDataSource                                   $dataSource
     * @param \Magento\Framework\Stdlib\DateTime\Timezone             $timezone
     * @param CalculationResultStorage                                                  $resultStorage
     */
    public function __construct(
        ConfigRepositoryInterface $configRepository,
        ObjectManagerInterface $objectManager,
        Config $config,
        LoggerInterface $logger,
        CalculationDataSource $dataSource,
        Timezone $timezone,
        CalculationResultStorage $resultStorage
    ) {
        parent::__construct($configRepository, $objectManager, $config, $logger, $dataSource);
        $this->timezone = $timezone;
        $this->resultStorage = $resultStorage;
    }

    /**
     * Calculate
     *
     * @param \Magento\Quote\Model\Quote                          $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param  Total $total
     * @return null|\Astound\AvaTax\Model\Service\Result\ResultInterface
     */
    public function calculate(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        $store = $quote->getStore();
        $this->dataSource->initAvataxData($shippingAssignment->getItems(), $store);
        $this->initRequest($store, $shippingAssignment, $total);

        $result = $this->resultStorage->getResult($this->request);

        if (!$this->canSendRequest($result)) {
            return $result;
        }

        $result = $this->send($store);

        $this->prepareResult($result);

        $this->resultStorage->setResult($this->request, $result);

        return $result;

    }

    /**
     * Init request
     *
     * @param Store                       $store
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total                       $total
     * @return $this
     */
    protected function initRequest($store, $shippingAssignment, $total)
    {
        $this->request = new Request();

        $header = $this->prepareHeader($store, $shippingAssignment->getShipping()->getAddress());
        $this->request->setHeader($header);

        $this->prepareLines($store, $shippingAssignment, $total);
        $this->request->setLines(array_values($this->lines));

        return $this;
    }

    /**
     * Prepare header
     *
     * @param \Magento\Store\Model\Store               $store
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @return \OnePica\AvaTax16\Document\Request\Header
     */
    protected function prepareHeader($store, $address)
    {
        $header = parent::prepareHeader($store, $address);
        $header->setDocumentCode('quote-' . $address->getId());
        $header->setTransactionDate(
            $this->timezone->scopeDate($store, null, true)->format('Y-m-d')
        );

        return $header;
    }

    /**
     * Prepare lines
     *
     * @param Store                       $store
     * @param ShippingAssignmentInterface $object
     * @param Total                       $total
     * @return $this
     */
    protected function prepareLines($store, $object, $total)
    {
        $this->lines = [];
        $this->addLine($this->prepareShippingLine($store, $total), $this->getShippingSku($store));
        $this->addLine($this->prepareGwOrderLine($store, $total), $this->getGwOrderSku($store));
        $this->addLine($this->prepareGwPrintedCardLine($store, $total), $this->getGwPrintedCardSku($store));
        $this->addItemsLine($store, $object->getItems());

        return $this;
    }

    /**
     * Prepare shipping line
     *
     * @param \Magento\Store\Model\Store $store
     * @param  Total                     $object
     * @return \OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareShippingLine($store, $object)
    {
        $line = parent::prepareShippingLine($store, $object);
        $shippingAmount = (float)$object->getData('base_shipping_amount');
        $discountAmount = (float)$object->getData('base_shipping_discount_amount');

        if ($this->dataSource->applyTaxAfterDiscount($store) && $discountAmount) {
            $line->setDiscounted('true');
            $shippingAmount -= $discountAmount;
            $shippingAmount = max(0, $shippingAmount);
        }

        $line->setLineAmount($shippingAmount);

        return $line;
    }

    /**
     * Prepare gw order line
     *
     * @param \Magento\Store\Model\Store $store
     * @param  Total                     $object
     * @return bool|false|\OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareGwOrderLine($store, $object)
    {
        $gwBasePrice = (float)$object->getData('gw_base_price');

        if (!$gwBasePrice) {
            return false;
        }

        $line = parent::prepareGwOrderLine($store, $object);
        $line->setAvalaraGoodsAndServicesType(
            $this->dataSource->getGwItemAvalaraGoodsAndServicesType($store)
        );
        $line->setLineAmount($gwBasePrice);

        return $line;
    }

    /**
     * Prepare gw printed card line
     *
     * @param \Magento\Store\Model\Store $store
     * @param  Total                     $object
     * @return false|\OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareGwPrintedCardLine($store, $object)
    {
        $basePrice = (float)$object->getData('gw_card_base_price');

        if (!$basePrice) {
            return false;
        }

        $line = parent::prepareGwPrintedCardLine($store, $object);
        $line->setAvalaraGoodsAndServicesType(
            $this->dataSource->getGwItemAvalaraGoodsAndServicesType($store)
        );
        $line->setLineAmount($basePrice);

        return $line;
    }

    /**
     * Add items line
     *
     * @param Store      $store
     * @param array|null $items
     * @return $this
     */
    protected function addItemsLine($store, $items)
    {
        if (!is_array($items)) {
            return $this;
        }

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($items as $item) {
            $this->addGwItemLine($store, $item);
            if ($this->isProductCalculated($item)) {
                continue;
            }

            $this->addLine($this->prepareItemLine($store, $item), $item->getId());
        }

        return $this;
    }

    /**
     * Prepare item line
     *
     * @param \Magento\Store\Model\Store      $store
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return false|\OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareItemLine($store, $item)
    {
        if (!(int)$item->getId()) {
            return false;
        }

        $basePrice = (float)$item->getBaseRowTotal();

        if ($this->dataSource->applyTaxAfterDiscount($store)) {
            $basePrice -= (float)$item->getBaseDiscountAmount();
        }

        $line = parent::prepareItemLine($store, $item);
        $line->setLineAmount($basePrice);
        $line->setItemCode($this->dataSource->getItemCode($item, $store));
        $line->setAvalaraGoodsAndServicesType(
            $this->dataSource->getItemAvalaraGoodsAndServicesType($item, $store)
        );
        $line->setNumberOfItems($item->getTotalQty());
        $line->setMetadata($this->dataSource->getItemMetaData($item, $store));

        return $line;
    }

    /**
     * Add gw item line
     *
     * @param Store                           $store
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return $this
     */
    protected function addGwItemLine($store, $item)
    {
        if (!(int)$item->getId()) {
            return false;
        }

        $line = $this->prepareGwItemLine($store, $item);
        if (!$line) {
            return $this;
        }

        $this->addLine($line, $this->getGwItemsSku($store));
        $this->itemGiftPair[$line->getLineCode()] = (int)$item->getId();

        return $this;
    }

    /**
     * Prepare gw item line
     *
     * @param \Magento\Store\Model\Store      $store
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return false|\OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareGwItemLine($store, $item)
    {
        if (!(int)$item->getData('gw_id')) {
            return false;
        }

        $line = parent::prepareGwItemLine($store, $item);
        $price = (float)$item->getData('gw_base_price') * (int)$item->getQty();
        $line->setLineAmount($price);

        return $line;
    }

    /**
     * @return bool
     */
    protected function hasDestinationAddress()
    {
        $defaultLocations = $this->request->getHeader()->getDefaultLocations();
        if ($defaultLocations && isset($defaultLocations[CalculationDataSource::TAX_LOCATION_PURPOSE_SHIP_TO])) {
            return true;
        }

        return false;
    }

    /**
     * Get item id by line
     *
     * @param Line $line
     * @return int|string
     */
    protected function getItemIdByLine($line)
    {
        return isset($this->itemGiftPair[$line->getLineCode()])
            ? $this->itemGiftPair[$line->getLineCode()]
            : $this->lineToItemId[$line->getLineCode()];
    }

    /**
     * Get item type by line
     *
     * @param Line $line
     * @return string
     */
    protected function getItemTypeByLine($line)
    {
        return isset($this->itemGiftPair[$line->getLineCode()]) ? 'gw_items' : 'items';
    }

    /**
     * Create result object
     *
     * @return \Astound\AvaTax\Model\Service\Result\Calculation
     */
    protected function createResultObject()
    {
        return $this->objectManager->create(\Astound\AvaTax\Model\Service\Result\Calculation::class);
    }

    /**
     * Get line rate
     *
     * @param Line $line
     * @return float
     */
    protected function getLineRate($line)
    {
        $rate = 0;
        if ($line->getCalculatedTax()->getTax()) {
            /** @var \OnePica\AvaTax16\Document\Response\Line\CalculatedTax\Details $detail */
            foreach ($line->getCalculatedTax()->getDetails() as $detail) {
                $rate += $detail->getRate();
            }
        }

        return $rate * 100;
    }

    /**
     * Get line fixed product tax
     *
     * @param Line $line
     * @return float
     */
    protected function getLineFptData($line)
    {
        $fpt = [
            'total_ftp_tax' => 0,
            'tax_details'   => []
        ];

        if (!$line->getCalculatedTax()->getTax()) {
            return $fpt;
        }

        /** @var \OnePica\AvaTax16\Document\Response\Line\CalculatedTax\Details $detail */
        foreach ($line->getCalculatedTax()->getDetails() as $detail) {
            if ((float)$detail->getRate()) {
                continue;
            }

            $fpt['tax_details'][] = [
                'tax'               => (float)$detail->getTax(),
                'jurisdiction_name' => $this->prepareJurisdictionName(
                    $detail->getTaxType(),
                    $detail->getJurisdictionName(),
                    $detail->getJurisdictionType()
                )
            ];

            $fpt['total_ftp_tax'] += (float)$detail->getTax();
        }

        return $fpt;
    }

    /**
     * Can send request
     *
     * @param \Astound\AvaTax\Model\Service\Result\Calculation $result
     * @return bool|null
     */
    protected function canSendRequest($result)
    {
        $canSendRequest = true;

        if ($result === null) {
            $canSendRequest &= true;
        } else {
            $canSendRequest &= !$result->getHasError();
            $canSendRequest &= !$result->hasItems();
        }

        $canSendRequest &= (bool)count($this->lineToItemId);
        $canSendRequest &= $this->hasDestinationAddress();
        $canSendRequest &= count($this->lines) > 1;//if request contain only shipping line we don`t need send request
        $canSendRequest &= !$this->stopRequest;

        return $canSendRequest;
    }

    /**
     * Prepare result
     *
     * @param \Astound\AvaTax\Model\Service\Result\Calculation $result
     * @return $this
     */
    protected function prepareResult($result)
    {
        $lines = (array)$result->getResponse()->getLines();

        /** @var \OnePica\AvaTax16\Document\Response\Line $line */
        foreach ($lines as $line) {
            $id = $this->getItemIdByLine($line);
            $type = $this->getItemTypeByLine($line);
            $rate = $this->getLineRate($line);
            $fptData = $this->getLineFptData($line);

            $amount = $fptData['total_ftp_tax']
                ? $line->getCalculatedTax()->getTax() - $fptData['total_ftp_tax']
                : $line->getCalculatedTax()->getTax();

            $result->setItemAmount($id, $amount, $type);
            $result->setItemFptData($id, $fptData, $type);
            $result->setItemRate($id, $rate, $type);
            $result->setItemJurisdictionData($id, $this->getItemJurisdictionData($line));
        }

        $result->setTimestamp((new DateTime())->getTimestamp());
        $result->setSummary($this->prepareSummary($result));

        return $this;
    }

    /**
     * Prepare summary
     *
     * @param \Astound\AvaTax\Model\Service\Result\Calculation $result
     * @return array
     */
    protected function prepareSummary($result)
    {
        $response = $result->getResponse();
        $rates = $this->getJurisdictionsRate($result);
        $summary = [];

        $calculatedSummary = $response->getCalculatedTaxSummary();
        if (null === $calculatedSummary || null === $calculatedSummary->getTaxByType()) {
            return $summary;
        }

        /** @var \OnePica\AvaTax16\Document\Response\CalculatedTaxSummary\TaxByType $value */
        foreach ($calculatedSummary->getTaxByType() as $type => $value) {
            /** @var \OnePica\AvaTax16\Document\Response\CalculatedTaxSummary\TaxByType\Details $data */
            foreach ($value->getJurisdictions() as $data) {
                $jurisdiction = $this->prepareJurisdictionName(
                    $type,
                    $data->getJurisdictionName(),
                    $data->getJurisdictionType()
                );

                if (!isset($rates[$jurisdiction])) {
                    continue;
                }

                $summary[] = array(
                    'name' => $jurisdiction,
                    'rate' => $rates[$jurisdiction],
                    'amt'  => $data->getTax()
                );
            }
        }

        return $summary;
    }

    /**
     * Get Jurisdictions rate array
     *
     * @param \Astound\AvaTax\Model\Service\Result\Calculation $result
     * @return array
     */
    protected function getJurisdictionsRate($result)
    {
        $response = $result->getResponse();
        $rates = [];

        /** @var \OnePica\AvaTax16\Document\Response\Line $line */
        foreach ($response->getLines() as $line) {
            if (!$line->getCalculatedTax()->getTax()) {
                continue;
            }

            /** @var \OnePica\AvaTax16\Document\Response\Line\CalculatedTax\Details $detail */
            foreach ($line->getCalculatedTax()->getDetails() as $detail) {
                $jurisdiction = $this->prepareJurisdictionName(
                    $detail->getTaxType(),
                    $detail->getJurisdictionName(),
                    $detail->getJurisdictionType()
                );

                if (!isset($rates[$jurisdiction]) && $detail->getRate()) {
                    $rates[$jurisdiction] = $detail->getRate() * 100;
                }
            }
        }

        return $rates;
    }

    /**
     * Get item jurisdiction data
     *
     * @param Line $line
     * @return array
     */
    protected function getItemJurisdictionData($line)
    {
        $rates = [];
        if ($line->getCalculatedTax()->getTax()) {
            /** @var \OnePica\AvaTax16\Document\Response\Line\CalculatedTax\Details $detail */
            foreach ($line->getCalculatedTax()->getDetails() as $detail) {
                $jurisdiction = $this->prepareJurisdictionName(
                    $detail->getTaxType(),
                    $detail->getJurisdictionName(),
                    $detail->getJurisdictionType()
                );
                $rates[$jurisdiction] = [
                    'rate' => $detail->getRate() * 100,
                    'tax' => $detail->getTax()
                ];
            }
        }

        return $rates;
    }

    /**
     * Prepare Jurisdiction name
     *
     * @param string $taxType
     * @param string $jurisdictionName
     * @param string $jurisdictionType
     * @return string
     */
    protected function prepareJurisdictionName($taxType, $jurisdictionName, $jurisdictionType)
    {
        $name = preg_replace('/(?<!\ )[A-Z]/', ' $0', $taxType) . ': '
                . $jurisdictionName . ' ' . $jurisdictionType;

        return ucfirst(trim($name));
    }
}
