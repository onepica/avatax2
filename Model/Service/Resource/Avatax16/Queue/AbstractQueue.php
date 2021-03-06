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
namespace Astound\AvaTax\Model\Service\Resource\Avatax16\Queue;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreFactory;
use Astound\AvaTax\Model\Service\Resource\AbstractResource;
use Astound\AvaTax\Model\Service\ConfigRepositoryInterface;
use Astound\AvaTax\Helper\Config;
use Astound\AvaTax\Api\Service\LoggerInterface;
use Astound\AvaTax\Model\Service\DataSource\Queue as QueueDataSource;
use OnePica\AvaTax16\Document\Request\Line;
use Astound\AvaTax\Model\Log;
use Astound\AvaTax\Model\Service\Result\ResultInterface;
use OnePica\AvaTax16\Document\Request;
use Astound\AvaTax\Model\Queue;

/**
 * Class AbstractQueue
 *
 * @package Astound\AvaTax\Model\Service\Resource\Avatax\Queue
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
     * Store factory
     *
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $storeFactory;

    /**
     * AbstractResource constructor.
     *
     * @param \Astound\AvaTax\Model\Service\ConfigRepositoryInterface $configRepository
     * @param \Magento\Framework\ObjectManagerInterface               $objectManager
     * @param \Astound\AvaTax\Helper\Config                           $config
     * @param \Astound\AvaTax\Api\Service\LoggerInterface             $logger
     * @param \Astound\AvaTax\Model\Service\DataSource\Queue          $dataSource
     * @param Timezone                                                $timezone
     * @param StoreFactory                                            $storeFactory
     */
    public function __construct(
        ConfigRepositoryInterface $configRepository,
        ObjectManagerInterface $objectManager,
        Config $config,
        LoggerInterface $logger,
        QueueDataSource $dataSource,
        Timezone $timezone,
        StoreFactory $storeFactory
    ) {
        parent::__construct($configRepository, $objectManager, $config, $logger, $dataSource);
        $this->timezone = $timezone;
        $this->storeFactory = $storeFactory;
    }

    /**
     * Queue submit
     * Send request object to service
     *
     * @param Queue $queue
     * @return ResultInterface
     */
    public function submit(Queue $queue)
    {
        $requestObject = unserialize($queue->getData('request_data'));
        $store = $this->storeFactory->create()->load($queue->getStoreId());
        $header = $requestObject->getHeader();
        $this->setCredentialsForHeader($header, $store);
        $this->request = $requestObject;
        $result = $this->send($store);
        return $result;
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
        /** @var \Astound\AvaTax\Model\Service\Avatax16\Config $config */
        try {
            $libResult = $config->getConnection()->createTransaction($this->request);
            $result->setResponse($libResult->toArray());
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
        /** @var \Magento\Sales\Model\Order\Item $orderItem */
        foreach ($orderItems as $orderItem) {
            $avataxData = $orderItem->getData('avatax_data');
            if ($orderItem->getChildrenItems() && !$this->isProductCalculated($orderItem)) {
                $avataxData = $orderItem->getChildrenItems()[0]->getData('avatax_data');
            }
            foreach ($objectItems as $item) {
                if ($item->getOrderItemId() == $orderItem->getId()) {
                    $item->setData('avatax_data', $avataxData);
                }
            }
        }
    }

    /**
     * Init request
     *
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $object
     * @return $this
     */
    protected function initRequest($object)
    {
        $this->request = new Request();
        $header = $this->prepareHeaderForObject($object);
        $this->request->setHeader($header);

        $this->prepareLines($object);
        $this->request->setLines(array_values($this->lines));

        return $this;
    }

    /**
     * Prepare header
     *
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $object
     * @return \OnePica\AvaTax16\Document\Request\Header
     */
    protected function prepareHeaderForObject($object)
    {
        $store = $object->getStore();
        $order = $object->getOrder();
        $shippingAddress = ($order->getShippingAddress()) ? $order->getShippingAddress() : $order->getBillingAddress();

        $objectDate = $this->convertGmtDate($object->getCreatedAt());
        $orderDate = $this->convertGmtDate($order->getCreatedAt());

        $header = parent::prepareHeader($store, $shippingAddress);
        $header->setDocumentCode($this->getDocumentCodeForObject($object));
        $header->setTransactionDate($objectDate);
        $header->setTaxCalculationDate($orderDate);

        return $header;
    }

    /**
     * Retrieve converted date taking into account the current time zone.
     *
     * @param string $gmt
     * @return string
     */
    protected function convertGmtDate($gmt)
    {
        return $this->timezone->date($gmt)->format('Y-m-d');
    }

    /**
     * Prepare lines
     *
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $object
     * @return $this
     */
    protected function prepareLines($object)
    {
        $this->lines = [];
        $store = $object->getStore();
        $credit = $this->isCredit();
        $this->addLine($this->prepareShippingLine($store, $object, $credit), $this->getShippingSku($store));
        $this->addLine($this->prepareGwOrderLine($store, $object, $credit), $this->getGwOrderSku($store));
        $this->addLine($this->prepareGwPrintedCardLine($store, $object, $credit), $this->getGwPrintedCardSku($store));
        $this->addLine($this->prepareGwItemsLine($store, $object, $credit), $this->getGwItemsSku($store));
        $this->addItemsLine($store, $object->getItems(), $credit);
        $this->addCustomLines($object);

        return $this;
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
            if ($this->isProductCalculated($item->getOrderItem())) {
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

        $price = $credit ? (-1 * $price) : $price;

        $line = parent::prepareItemLine($store, $item);
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
     * Add custom lines
     *
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $object
     * @return $this
     */
    protected function addCustomLines($object)
    {
        return $this;
    }

    /**
     * Get Service Request Object
     *
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $object
     * @return mixed
     */
    public function getServiceRequestObject($object)
    {
        $store = $object->getStore();
        // Copy Avatax data from order items to object items, because only order items contains this data
        $this->copyAvataxDataFromOrderItemsToObjectItems($object);
        $this->dataSource->initAvataxData($object->getItems(), $store);
        $this->initRequest($object);
        return $this->request;
    }

    /**
     * Get if items is for credit
     *
     * @return bool
     */
    abstract protected function isCredit();

    /**
     * Get document code for object
     *
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $object
     * @return string
     */
    abstract protected function getDocumentCodeForObject($object);
}
