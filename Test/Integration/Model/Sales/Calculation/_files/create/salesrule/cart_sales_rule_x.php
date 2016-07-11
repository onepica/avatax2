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

try
{
    $objectManager = Bootstrap::getObjectManager();

    /** @var \Magento\Framework\Registry $registry */
    $registry = $objectManager->get('Magento\Framework\Registry');
    $fixtureData = $registry->registry("fixture");

    /** @var \Magento\SalesRule\Model\Rule $salesRule */
    $salesRule = $objectManager->create(\Magento\SalesRule\Model\Rule::class);
    $salesRule->setData(
        [
            'name' => 'Off on Amount > 20$',
            'is_active' => 1,
            'customer_group_ids' => [\Magento\Customer\Model\GroupManagement::NOT_LOGGED_IN_ID, 3, 1],
            'coupon_type' => \Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC,
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

    // type SPECIFIC with code
    /** @var \Magento\SalesRule\Model\Coupon $coupon */
    $coupon = $objectManager->create(\Magento\SalesRule\Model\Coupon::class);
    $coupon->loadByCode('10OFF');
    $coupon->setRuleId($salesRule->getId())
        ->setCode('10OFF')
        ->setType(0)
        ->save();

}
catch(Exception $ex)
{
    $message = $ex->getMessage();
}
