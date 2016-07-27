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
$registry = $objectManager->get('Magento\Framework\Registry');
$fixtureData = $registry->registry("fixture");

$fixtureCustomerID = $fixtureData->getCustomerId();

/** @var \Magento\Customer\Model\Address $customerAddress */
$customerAddress = Bootstrap::getObjectManager()->create('Magento\Customer\Model\Address');
$customerAddress->isObjectNew(true);
$customerAddress->setCustomerId($fixtureCustomerID)->setData(
    [
        //'entity_id'        => 1,
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

if ($fixtureData->getAddress()) {
    $customerAddress->addData($fixtureData->getAddress());
}

$customerAddress->save();

$fixtureData->setCustomerAddressId($customerAddress->getId());

//save customer address to customer
/** @var Magento\Customer\Model\Customer $customer */
$customer = $objectManager->create(Magento\Customer\Model\Customer::class);
$customer->load($fixtureCustomerID);
$customer->addAddress($customerAddress);
$customer->save();
