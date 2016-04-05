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

/*        $this->prepareLines($creditmemo);
        $this->request->setLines(array_values($this->lines));*/

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
     * Creditmemo
     *
     * @param Queue $queue
     * @return ResultInterface
     */
    public function creditmemo(Queue $queue)
    {
        // TODO: Implement creditmemo() method.
    }
}
