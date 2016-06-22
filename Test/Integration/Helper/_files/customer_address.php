<?php

use Magento\TestFramework\Helper\Bootstrap;

/** @var \Magento\Customer\Model\Address $customerAddress */
$customerAddress = Bootstrap::getObjectManager()->create('Magento\Customer\Model\Address');
$customerAddress->isObjectNew(true);
$customerAddress->setCustomerId(1)->setData(
    [
        'entity_id'        => 1,
        'attribute_set_id' => 2,
        'telephone'        => 123456789,
        'postcode'         => 10038,
        'country_id'       => 'US',
        'city'             => 'NewYork',
        'company'          => 'CompanyName',
        'street'           => '350th 5 Ave',
        'lastname'         => 'Smith',
        'firstname'        => 'John',
        'parent_id'        => 1,
        'region_id'        => 43,
    ]
);
$customerAddress->save();

$objectManager = Bootstrap::getObjectManager();
$objectManager->get('Magento\Framework\Registry')->register('astound_avatax_customer_address', $customerAddress);