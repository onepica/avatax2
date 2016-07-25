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

require '/../../../../../_files/scenario/data/init_fixture.php';
require '/../../../../../_files/scenario/data/init_expected.php';

use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var \Magento\Framework\Registry $registry */
$registry = $objectManager->get('Magento\Framework\Registry');

/** @var \Magento\Framework\DataObject $data */
$data = $registry->registry('fixture');
$data->addData(
    array(
        "fixture_uniqid"=>uniqid(),
        "product_price"=>100,
        'shipping_method'=>'flatrate_flatrate',
        "product_qty"=>1,
        'giftcardaccount'=>array('balance'=>9.99)
    )
);

$expected = $registry->registry('expected');
$expected->addData(
    array(
        "product_price"          => 100,
        "product_qty"            => 1,
        "total_tax"              => 8.97,
        'shipping_cost'          => 9.18,
        'giftcardaccount_amount' => -9.99,
        'grand_total'            => 100.01,
        'config'                 => array(
            'store' => array(
                'default' => array(
                    'tax/calculation/price_includes_tax' => 1,
                    'carriers/flatrate/price'            => 10
                )
            )
        )
    )
);

/** @var Astound\AvaTax\Helper\Config $scopeConfig */
$scopeConfig = $objectManager->create(\Astound\AvaTax\Helper\Config::class);
$number = $scopeConfig->getServiceAccountNumber();

require '/../../../../../_files/create/customer.php';
require '/../../../../../_files/create/customer/address.php';
require '/../../../../../_files/create/product/simple.php';
require '/../../../../../_files/create/giftcard/codes_pool.php';
require '/../../../../../_files/create/giftcard/giftcardaccount.php';
require '/../../../../../_files/create/quote.php';
require '/../../../../../_files/create/giftcard/quote_with_giftcard_saved.php';
