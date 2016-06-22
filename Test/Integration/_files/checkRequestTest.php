<?php
use Magento\TestFramework\Helper\Bootstrap;

require_once __DIR__ . '/customer.php';
require_once __DIR__ . '/customer_address.php';
require_once __DIR__ . '/customer_group.php';
require_once __DIR__ . '/products.php';


$objectManager = Bootstrap::getObjectManager();

/** @var \Magento\Tax\Model\ClassModel $customerTaxClass */
$customerTaxClass = $objectManager->create('Magento\Tax\Model\ClassModel');
$fixtureCustomerTaxClass = 'Retail Customer';
$customerTaxClass->load($fixtureCustomerTaxClass, 'class_name');

$fixtureCustomerId = 1;
/** @var \Magento\Customer\Model\Customer $customer */
$customer = $objectManager->create('Magento\Customer\Model\Customer')->load($fixtureCustomerId);

/** @var \Magento\Customer\Model\Group $customerGroup */
$customerGroup = $objectManager->create('Magento\Customer\Model\Group')->load('custom_group', 'customer_group_code');
$customerGroup->setTaxClassId($customerTaxClass->getId())->save();

$customer->setGroupId($customerGroup->getId())->save();

/** @var \Magento\Tax\Model\ClassModel $productTaxClass */
$productTaxClass = $objectManager->create('Magento\Tax\Model\ClassModel');
$fixtureProductTaxClass = 'Taxable Goods';
$productTaxClass->load($fixtureProductTaxClass, 'class_name');

$fixtureProductId = 1;
/** @var \Magento\Catalog\Model\Product $product */
$product = $objectManager->create('Magento\Catalog\Model\Product')->load($fixtureProductId);
$product->setTaxClassId($productTaxClass->getId())->save();

$fixtureCustomerAddressId = 1;


/** @var \Magento\Quote\Model\Quote\Address $quoteShippingAddress */
$quoteShippingAddress = $objectManager->create('Magento\Quote\Model\Quote\Address');
/** @var \Magento\Customer\Api\AddressRepositoryInterface $addressRepository */
$addressRepository = $objectManager->create('Magento\Customer\Api\AddressRepositoryInterface');
$address = $quoteShippingAddress->importCustomerAddressData($addressRepository->getById($fixtureCustomerAddressId));
/** @var \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository */
$customerRepository = $objectManager->create('Magento\Customer\Api\CustomerRepositoryInterface');
/** @var \Magento\Quote\Model\Quote $quote */
$quote = $objectManager->create('Magento\Quote\Model\Quote');

$quote->setStoreId(1)
    ->setIsActive(true)
    ->setIsMultiShipping(false)
    ->assignCustomerWithAddressChange($customerRepository->getById($customer->getId()))
    ->setShippingAddress($address)
    ->setBillingAddress($address)
    ->setCheckoutMethod('customer')
    ->setPasswordHash($customer->encryptPassword($customer->getPassword()))
    ->addProduct($product->load($product->getId()), 2);

$quote->save();
$quote->load($quote->getId())
    ->getShippingAddress()->setShippingMethod('flatrate_flatrate')
    ->setShippingDescription('Flat Rate - Fixed')
    ->setCollectShippingRates(1)
    ->setShippingAmount(10.0)
    ->setBaseShippingAmount(10.0)
    ->save();

$quote->setHasDataChanges(true);
$quote->save();

$objectManager->get('Magento\Framework\Registry')->register('astound_avatax_quote_id', $quote->getId());