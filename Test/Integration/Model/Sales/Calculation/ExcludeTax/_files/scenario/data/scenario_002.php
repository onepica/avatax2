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

require '/../../../../_files/scenario/data/init_fixture.php';
require '/../../../../_files/scenario/data/init_expected.php';

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
        "product_qty"=>2
    )
);

$expected = $registry->registry('expected');
$expected->addData(
    array(
        "product_price" => 100,
        "product_qty"   => 2,
        "total_tax"     => 19.53,
        'shipping_cost' => 20,
        'grand_total'   => 239.53,
        'config'        => array(
            'store' => array(
                'default' => array(
                    'tax/calculation/price_includes_tax' => 0,
                    'carriers/flatrate/price'            => 10
                )
            )
        )
    )
);

require '/../../../../_files/create/customer.php';
require '/../../../../_files/create/customer/address.php';
require '/../../../../_files/create/product/simple.php';
require '/../../../../_files/create/quote.php';