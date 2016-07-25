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

/** @var \Magento\GiftCardAccount\Model\ResourceModel\Pool $poolResourceModel */
$poolResourceModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\GiftCardAccount\Model\ResourceModel\Pool'
);
// clean up pool
$poolResourceModel->cleanupByStatus(\Magento\GiftCardAccount\Model\Pool\AbstractPool::STATUS_FREE);
$poolResourceModel->cleanupByStatus(\Magento\GiftCardAccount\Model\Pool\AbstractPool::STATUS_USED);
// insert codes to pool
$poolResourceModel->saveCode('fixture_code_1');
$poolResourceModel->saveCode('fixture_code_2');
$poolResourceModel->saveCode('fixture_code_3');

/** @var \Magento\GiftCardAccount\Model\Pool $poolModel */
$poolModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\GiftCardAccount\Model\Pool');
$poolModel->setCode('fixture_code_1')->setStatus(\Magento\GiftCardAccount\Model\Pool\AbstractPool::STATUS_USED);
$poolModel->save();
