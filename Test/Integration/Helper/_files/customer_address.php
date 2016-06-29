<?php

use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var \Magento\Customer\Model\Address $customerAddress */
$customerAddress = $objectManager->create('Magento\Customer\Model\Address');
$customerAddress->isObjectNew(true);

$addressData = include __DIR__ . '/address_data.php';

$customerAddress->setCustomerId(1)->setData($addressData);
$customerAddress->save();


$objectManager->get('Magento\Framework\Registry')->register('astound_avatax_customer_address', $customerAddress);