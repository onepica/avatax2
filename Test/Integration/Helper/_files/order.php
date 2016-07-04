<?php
require __DIR__ . '/product_simple.php';
/** @var \Magento\Catalog\Model\Product $product */

$addressData = include __DIR__ . '/address_data.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$billingAddress = $objectManager->create('Magento\Sales\Model\Order\Address', ['data' => $addressData]);
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');

$payment = $objectManager->create('Magento\Sales\Model\Order\Payment');
$payment->setMethod('checkmo');

/** @var \Magento\Sales\Model\Order\Item $orderItem */
$orderItem = $objectManager->create('Magento\Sales\Model\Order\Item');
$orderItem->setProductId($product->getId())->setQtyOrdered(2);
$orderItem->setBasePrice($product->getPrice());
$orderItem->setPrice($product->getPrice());
$orderItem->setRowTotal($product->getPrice());
$orderItem->setProductType('simple');

/** @var \Magento\Sales\Model\Order $order */
$order = $objectManager->create('Magento\Sales\Model\Order');
$order->setIncrementId(
    '100000001'
)->setState(
        \Magento\Sales\Model\Order::STATE_PROCESSING
    )->setStatus(
        $order->getConfig()->getStateDefaultStatus(\Magento\Sales\Model\Order::STATE_PROCESSING)
    )->setSubtotal(
        100
    )->setBaseSubtotal(
        100
    )->setBaseGrandTotal(
        100
    )->setCustomerIsGuest(
        true
    )->setCustomerEmail(
        'customer@null.com'
    )->setCustomerGroupId(
        0
    )->setBillingAddress(
        $billingAddress
    )->setShippingAddress(
        $shippingAddress
    )->setStoreId(
        $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId()
    )->addItem(
        $orderItem
    )->setPayment(
        $payment
    );
$order->save();
