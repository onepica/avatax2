<?php
$addressData = array(
    'entity_id'        => 1,
    'attribute_set_id' => 2,
    'telephone'        => 'xxxxxxxxxx',
    'postcode'         => 10038,
    'country_id'       => 'US',
    'city'             => 'NewYork',
    'company'          => 'CompanyName',
    'street'           => '110 Wiliam St',
    'lastname'         => 'Smith',
    'firstname'        => 'John',
    'parent_id'        => 1,
    'region_id'        => 43,
    'address_type'     => \Magento\Quote\Model\Quote\Address::ADDRESS_TYPE_SHIPPING
);

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Quote\Model\Quote\Address $quoteShippingAddress */
$quoteShippingAddress = $objectManager->create('Magento\Quote\Model\Quote\Address');
$quoteShippingAddress->setData($addressData);

$objectManager->get('Magento\Framework\Registry')->register('astound_avatax_quote_address_normalizable', $quoteShippingAddress);
