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
namespace OnePica\AvaTax\Model\Service\Resource\Avatax16\Queue;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Store\Model\Store;
use OnePica\AvaTax\Model\Service\Resource\AbstractResource;
use OnePica\AvaTax\Api\ConfigRepositoryInterface;
use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Api\Service\LoggerInterface;
use OnePica\AvaTax\Model\Service\DataSourceQueue;
use OnePica\AvaTax16\Document\Request\Line;
use OnePica\AvaTax\Model\Log;
use OnePica\AvaTax\Api\ResultInterface;

/**
 * Class AbstractQueue
 *
 * @package OnePica\AvaTax\Model\Service\Resource\Avatax\Queue
 */
abstract class AbstractQueue extends AbstractResource
{
    /**
     * Timezone
     *
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $timezone;

    /**
     * AbstractResource constructor.
     *
     * @param \OnePica\AvaTax\Api\ConfigRepositoryInterface $configRepository
     * @param \Magento\Framework\ObjectManagerInterface     $objectManager
     * @param \OnePica\AvaTax\Helper\Config                 $config
     * @param \OnePica\AvaTax\Api\Service\LoggerInterface   $logger
     * @param \OnePica\AvaTax\Model\Service\DataSourceQueue $dataSource
     * @param Timezone $timezone
     */
    public function __construct(
        ConfigRepositoryInterface $configRepository,
        ObjectManagerInterface $objectManager,
        Config $config,
        LoggerInterface $logger,
        DataSourceQueue $dataSource,
        Timezone $timezone
    ) {
        parent::__construct($configRepository, $objectManager, $config, $logger, $dataSource);
        $this->timezone = $timezone;
    }

    /**
     * Retrieve converted date taking into account the current time zone and store.
     *
     * @param string $gmt
     * @param Store  $store
     * @return string
     */
    protected function convertGmtDate($gmt, $store)
    {
        return $this->timezone->scopeDate($store, $gmt)->format('Y-m-d');
    }

    /**
     * Prepare shipping line
     *
     * @param \Magento\Store\Model\Store                                               $store
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $object
     * @param  bool                                                                    $credit
     * @return \OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareShippingLine($store, $object, $credit = false)
    {
        $line = parent::prepareShippingLine($store, $object);
        $shippingAmount = (float)$object->getBaseShippingAmount();
        $discountAmount = (float)$object->getOrder()->getBaseShippingDiscountAmount();

        if ($this->dataSource->taxIncluded($store)) {
            $shippingAmount = (float)$object->getBaseShippingInclTax();
        }

        if ($this->dataSource->applyTaxAfterDiscount($store) && $discountAmount) {
            $line->setDiscounted('true');
            $shippingAmount = max(0, $shippingAmount -= $discountAmount);
        }

        $shippingAmount = $credit ? (-1 * $shippingAmount) : $shippingAmount;
        $line->setLineAmount($shippingAmount);

        return $line;
    }

    /**
     * Prepare gw order line
     *
     * @param \Magento\Store\Model\Store                                               $store
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $object
     * @param  bool                                                                    $credit
     * @return bool|false|\OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareGwOrderLine($store, $object, $credit = false)
    {
        $amount = (float)$object->getData('gw_base_price');

        if (!$amount) {
            return false;
        }

        $line = parent::prepareGwOrderLine($store, $object);
        $line->setAvalaraGoodsAndServicesType(
            $this->dataSource->getGwItemAvalaraGoodsAndServicesType($store)
        );

        if ($this->dataSource->taxIncluded($store)) {
            $amount += $object->getGwBaseTaxAmount();
        }

        $amount = $credit ? (-1 * $amount) : $amount;
        $line->setLineAmount($amount);

        return $line;
    }

    /**
     * Prepare gw printed card line
     *
     * @param \Magento\Store\Model\Store                                               $store
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $object
     * @param  bool                                                                    $credit
     * @return false|\OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareGwPrintedCardLine($store, $object, $credit = false)
    {
        $amount = (float)$object->getData('gw_card_base_price');

        if (!$amount) {
            return false;
        }

        $line = parent::prepareGwPrintedCardLine($store, $object);
        $line->setAvalaraGoodsAndServicesType(
            $this->dataSource->getGwItemAvalaraGoodsAndServicesType($store)
        );

        if ($this->dataSource->taxIncluded($store)) {
            $amount += $object->getGwCardBaseTaxAmount();
        }

        $amount = $credit ? (-1 * $amount) : $amount;
        $line->setLineAmount($amount);

        return $line;
    }

    /**
     * Add items line
     *
     * @param Store      $store
     * @param array|null $items
     * @param  bool      $credit
     * @return $this
     */
    protected function addItemsLine($store, $items, $credit = false)
    {
        if (empty($items)) {
            return $this;
        }

        /** @var \Magento\Sales\Model\Order\Invoice\Item|\Magento\Sales\Model\Order\Creditmemo\Item $item */
        foreach ($items as $item) {
            if ($this->isProductCalculated($item)) {
                continue;
            }

            $this->addLine($this->prepareItemLine($store, $item, $credit), $item->getId());
        }

        return $this;
    }

    /**
     * Prepare item line
     *
     * @param \Magento\Store\Model\Store                                                         $store
     * @param \Magento\Sales\Model\Order\Invoice\Item|\Magento\Sales\Model\Order\Creditmemo\Item $item
     * @param  bool                                                                              $credit
     * @return false|\OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareItemLine($store, $item, $credit = false)
    {
        if (!(int)$item->getId()) {
            return false;
        }

        $price = (float)$item->getBaseRowTotal();

        if ($this->dataSource->taxIncluded($store)) {
            $price = (float)$item->getBaseRowTotalInclTax();
        }

        if ($this->dataSource->applyTaxAfterDiscount($store)) {
            $price -= (float)$item->getBaseDiscountAmount();
        }

        $line = parent::prepareItemLine($store, $item);

        $price = $credit ? (-1 * $price) : $price;
        $line->setLineAmount($price);
        $line->setItemCode($this->dataSource->getItemCode($item, $store));
        $line->setAvalaraGoodsAndServicesType(
            $this->dataSource->getItemAvalaraGoodsAndServicesType($item, $store)
        );
        $line->setNumberOfItems($item->getQty());
        $line->setMetadata($this->dataSource->getItemMetaData($item, $store));

        return $line;
    }

    /**
     * Prepare gw item line
     *
     * @param \Magento\Store\Model\Store                                               $store
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $object
     * @param  bool                                                                    $credit
     * @return false|\OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareGwItemsLine($store, $object, $credit = false)
    {
        if (!(int)$object->getData('gw_items_base_price')) {
            return false;
        }

        $line = new Line();
        $line->setLineCode($this->getNewLineCode());
        $line->setItemCode($this->getGwItemsSku($store));
        $line->setItemDescription(self::DEFAULT_GW_ITEMS_DESCRIPTION);
        $line->setAvalaraGoodsAndServicesType($this->dataSource->getGwItemAvalaraGoodsAndServicesType($store));
        $line->setNumberOfItems(1);
        $line->setDiscounted('false');
        $line->setTaxIncluded($this->dataSource->taxIncluded($store) ? 'true' : 'false');

        $amount = (float)$object->getData('gw_items_base_price');
        if ($this->dataSource->taxIncluded($store)) {
            $amount += $object->getGwItemsBaseTaxAmount();
        }
        $amount = $credit ? (-1 * $amount) : $amount;
        $line->setLineAmount($amount);

        return $line;
    }

    /**
     * Copy Avatax Data from Order Items To Object Items
     *
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $object
     * @return $this
     */
    protected function copyAvataxDataFromOrderItemsToObjectItems($object)
    {
        $orderItems = $object->getOrder()->getItems();
        $objectItems = $object->getItems();
        foreach ($orderItems as $orderItem) {
            $avataxData = $orderItem->getData('avatax_data');
            foreach ($objectItems as $item) {
                if ($item->getOrderItemId() == $orderItem->getId()) {
                    $item->setData('avatax_data', $avataxData);
                }
            }
        }
    }

    /**
     * Send request
     *
     * @param Store $store
     * @return ResultInterface
     */
    protected function send($store)
    {
        $result = $this->createResultObject();

        $config = $this->configRepository->getConfigByStore($store);
        /** @var \OnePica\AvaTax\Model\Service\Avatax16\Config $config */
        try {
            $libResult = $config->getConnection()->createTransaction($this->request);
            $result->setResponse($libResult);
            $result->setHasError($libResult->getHasError());
            $result->setErrors($libResult->getErrors());

            if (!$libResult->getHasError()) {
                $totalTax = $libResult->getCalculatedTaxSummary()->getTotalTax();
                $result->setTotalTax($totalTax);
                $documentCode = $libResult->getHeader()->getDocumentCode();
                $result->setDocumentCode($documentCode);
            } elseif (!$libResult->getErrors()) {
                $result->setErrors([__('The user or account could not be authenticated.')]);
            }
        } catch (\Exception $e) {
            $result->setHasError(true);
            $result->setErrors([$e->getMessage()]);
        }

        $this->logger->log(
            Log::TRANSACTION,
            $this->request->toArray(),
            $result,
            $store->getId(),
            $config->getConnection()
        );

        return $result;
    }
}
