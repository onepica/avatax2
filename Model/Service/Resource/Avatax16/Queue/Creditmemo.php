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

use OnePica\AvaTax\Api\ResultInterface;
use OnePica\AvaTax\Api\Service\CreditmemoResourceInterface;
use OnePica\AvaTax\Model\Queue;
use OnePica\AvaTax16\Document\Request;
use OnePica\AvaTax16\Document\Request\Line;
use OnePica\AvaTax\Model\Service\Result\Creditmemo as CreditmemoResult;

/**
 * Class Creditmemo
 *
 * @package OnePica\AvaTax\Model\Service\Resource\Avatax\Queue
 */
class Creditmemo extends AbstractQueue implements CreditmemoResourceInterface
{
    /**
     * Get Creditmemo Service Request Object
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return mixed
     */
    public function getCreditmemoServiceRequestObject(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $store = $creditmemo->getStore();
        // Copy Avatax data from order items to creditmemo items, because only order items contains this data
        $this->copyAvataxDataFromOrderItemsToObjectItems($creditmemo);
        $this->dataSource->initAvataxData($creditmemo->getItems(), $store);
        $this->initRequest($creditmemo);
        return $this->request;
    }

    /**
     * Init request
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    protected function initRequest($creditmemo)
    {
        $this->request = new Request();
        $header = $this->prepareHeaderForCreditmemo($creditmemo);
        $this->request->setHeader($header);

        $this->prepareLines($creditmemo);
        $this->request->setLines(array_values($this->lines));

        return $this;
    }

    /**
     * Prepare header
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return \OnePica\AvaTax16\Document\Request\Header
     */
    protected function prepareHeaderForCreditmemo($creditmemo)
    {
        $store = $creditmemo->getStore();
        $order = $creditmemo->getOrder();
        $shippingAddress = ($order->getShippingAddress()) ? $order->getShippingAddress() : $order->getBillingAddress();
        $creditmemoDate = $this->convertGmtDate($creditmemo->getCreatedAt(), $store);
        $orderDate = $this->convertGmtDate($creditmemo->getCreatedAt(), $store);

        $header = parent::prepareHeader($store, $shippingAddress);
        $header->setDocumentCode($this->getCreditmemoDocumentCode($creditmemo));
        $header->setTransactionDate($creditmemoDate);
        $header->setTaxCalculationDate($orderDate);

        return $header;
    }

    /**
     * Get document code for creditmemo
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return string
     */
    protected function getCreditmemoDocumentCode($creditmemo)
    {
        return self::DOCUMENT_CODE_CREDITMEMO_PREFIX . $creditmemo->getIncrementId();
    }

    /**
     * Prepare lines
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    protected function prepareLines($creditmemo)
    {
        $this->lines = [];
        $store = $creditmemo->getStore();
        $this->addLine($this->prepareShippingLine($store, $creditmemo, true), $this->getShippingSku($store));
        $this->addLine($this->prepareGwOrderLine($store, $creditmemo, true), $this->getGwOrderSku($store));
        $this->addLine($this->prepareGwPrintedCardLine($store, $creditmemo, true), $this->getGwPrintedCardSku($store));
        $this->addLine($this->prepareGwItemsLine($store, $creditmemo, true), $this->getGwItemsSku($store));
        $this->addItemsLine($store, $creditmemo->getItems(), true);
        $this->addAdjustmentsLines($creditmemo);

        return $this;
    }

    /**
     * Add Adjustments lines
     *
     * @param  \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    protected function addAdjustmentsLines($creditmemo)
    {
        $store = $creditmemo->getStore();
        $this->addLine(
            $this->prepareAdjustmentPositiveLine($store, $creditmemo->getBaseAdjustmentPositive()),
            $this->getAdjustmentsPositiveSku($store)
        );
        $this->addLine(
            $this->prepareAdjustmentNegativeLine($store, $creditmemo->getBaseAdjustmentNegative()),
            $this->getAdjustmentsNegativeSku($store)
        );

        return $this;
    }

    /**
     * Prepare Adjustment Positive Line
     *
     * @param \Magento\Store\Model\Store                     $store
     * @param  float                                         $adjustmentPositive
     * @return false|\OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareAdjustmentPositiveLine($store, $adjustmentPositive)
    {
        $line = false;
        if ($adjustmentPositive != 0) {
            $price = $adjustmentPositive * (-1);
            $line = new Line();
            $line->setLineCode($this->getNewLineCode());
            $line->setItemCode($this->getAdjustmentsPositiveSku($store));
            $line->setItemDescription(self::DEFAULT_ADJUSTMENT_POSITIVE_DESCRIPTION);
            $line->setAvalaraGoodsAndServicesType($this->getAdjustmentsPositiveSku($store));
            $line->setNumberOfItems(1);
            $line->setDiscounted('false');
            $line->setLineAmount($price);
        }

        return $line;
    }

    /**
     * Prepare Adjustment Negative Line
     *
     * @param \Magento\Store\Model\Store                     $store
     * @param  float                                         $adjustmentNegative
     * @return false|\OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareAdjustmentNegativeLine($store, $adjustmentNegative)
    {
        $line = false;
        if ($adjustmentNegative != 0) {
            $price = $adjustmentNegative;
            $line = new Line();
            $line->setLineCode($this->getNewLineCode());
            $line->setItemCode($this->getAdjustmentsNegativeSku($store));
            $line->setItemDescription(self::DEFAULT_ADJUSTMENT_NEGATIVE_DESCRIPTION);
            $line->setAvalaraGoodsAndServicesType($this->getAdjustmentsNegativeSku($store));
            $line->setNumberOfItems(1);
            $line->setDiscounted('false');
            $line->setLineAmount($price);
        }

        return $line;
    }

    /**
     * Creditmemo
     *
     * @param Queue $queue
     * @return ResultInterface
     */
    public function creditmemo(Queue $queue)
    {
        $requestObject = unserialize($queue->getData('request_data'));
        $this->request = $requestObject;
        $store = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($queue->getStoreId());
        $result = $this->send($store);
        return $result;
    }

    /**
     * Get result object
     *
     * @return \OnePica\AvaTax\Model\Service\Result\Creditmemo
     */
    protected function createResultObject()
    {
        return $this->objectManager->create(CreditmemoResult::class);
    }
}
