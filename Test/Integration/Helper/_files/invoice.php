<?php
/**
 * Paid invoice fixture.
 */

require 'order.php';
/** @var \Magento\Sales\Model\Order $order */

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$orderService = $objectManager->create(
    'Magento\Sales\Api\InvoiceManagementInterface'
);
$invoice = $orderService->prepareInvoice($order);
$invoice->register();
$order = $invoice->getOrder();
$order->setIsInProcess(true);
$transactionSave = $objectManager->create('Magento\Framework\DB\Transaction');
$transactionSave->addObject($invoice)->addObject($order)->save();

$objectManager->get('Magento\Framework\Registry')->register('astound_avatax_invoice', $invoice);
