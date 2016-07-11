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

use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var \Magento\Framework\Registry $registry */
$registry = $objectManager->get(\Magento\Framework\Registry::class);
$fixtureData = $registry->registry("fixture");

$fixtureCustomerId = $fixtureData->getCustomerId();

$fixtureCustomerAddressId = $fixtureData->getCustomerAddressId();
$fixtureProductId = $fixtureData->getProductId();
$fixtureProductQty = $fixtureData->getProductQty();
$shippingMethod = $fixtureData->getShippingMethod();

$customer = $objectManager->create('Magento\Customer\Model\Customer')->load($fixtureCustomerId);

/** @var \Magento\Catalog\Model\Product $product */
$product = $objectManager->create('Magento\Catalog\Model\Product')->load($fixtureProductId);

/** @var \Magento\Quote\Model\Quote\Address $quoteShippingAddress */
$quoteShippingAddress = $objectManager->create('Magento\Quote\Model\Quote\Address');
/** @var \Magento\Customer\Api\AddressRepositoryInterface $addressRepository */
$addressRepository = $objectManager->create('Magento\Customer\Api\AddressRepositoryInterface');
$address = $quoteShippingAddress->importCustomerAddressData($addressRepository->getById($fixtureCustomerAddressId));

/** @var \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository */
$customerRepository = $objectManager->create('Magento\Customer\Api\CustomerRepositoryInterface');

/** @var \Magento\Quote\Model\Quote $quote */
$quote = $objectManager->create(\Magento\Quote\Model\Quote::class);

//$coll = $quote->getCollection()->load();
//foreach ($coll as $q) {
//    $q->delete();
//}

$quote->setStoreId(1)
    ->setIsActive(true)
    ->setIsMultiShipping(false)
    ->assignCustomerWithAddressChange($customerRepository->getById($customer->getId()))
    ->setShippingAddress($address)
    ->setBillingAddress($address)
    ->setCheckoutMethod('customer')
    ->setPasswordHash($customer->encryptPassword($customer->getPassword()))
    ->addProduct($product->load($fixtureProductId), $fixtureProductQty);

$quote->save();
$quote->load($quote->getId())
    ->getShippingAddress()->setShippingMethod($shippingMethod)
    ->setShippingDescription("Shipping method : {$shippingMethod}")
    ->setCollectShippingRates(1)
    ->save();

$quote->setHasDataChanges(true);
$quote->save();

$fixtureData->setQuoteId($quote->getId());

