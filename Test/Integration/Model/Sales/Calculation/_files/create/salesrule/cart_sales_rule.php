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

/** @var \Magento\SalesRule\Model\Rule $salesRule */
$salesRule = $objectManager->create(\Magento\SalesRule\Model\Rule::class);
$coll = $salesRule->getCollection();
$coll->load();
foreach ($coll as $sr) {
    $sr->delete();
}
$salesRule->setData(
    [
        'name' => 'Off on Amount > 20$',
        'is_active' => 1,
        'customer_group_ids' => [\Magento\Customer\Model\GroupManagement::NOT_LOGGED_IN_ID, 3, 1],
        'coupon_type' => \Magento\SalesRule\Model\Rule::COUPON_TYPE_NO_COUPON,
        'conditions' => [
            [
                'type' => 'Magento\SalesRule\Model\Rule\Condition\Address',
                'attribute' => 'base_subtotal',
                'operator' => '>',
                'value' => 20
            ]
        ],
        'simple_action' => 'by_percent',
        'discount_amount' => 0,
        'stop_rules_processing' => 1,
        'website_ids' => [
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Store\Model\StoreManagerInterface'
            )->getWebsite()->getId()
        ]
    ]
);

if ($fixtureData->getCartSalesRule()) {
    $salesRule->addData($fixtureData->getCartSalesRule());
}
$salesRule->save();


$fixtureData->setSalesRuleId($salesRule->getId());

$rule = $objectManager->create(\Magento\SalesRule\Model\Rule::class);
$rule->load($salesRule->getId());
