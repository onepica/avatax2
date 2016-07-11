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

use \Magento\TestFramework\Helper\Bootstrap;
use \Magento\GiftCardAccount\Model\Giftcardaccount;
use \Magento\Store\Model\StoreManagerInterface;

/** @var \Magento\Framework\Registry $registry */
$registry = $objectManager->get(Magento\Framework\Registry::class);
$fixtureData = $registry->registry("fixture");

/** @var $model \Magento\GiftCardAccount\Model\Giftcardaccount */
$model = Bootstrap::getObjectManager()->create(Giftcardaccount::class);

$model
    ->setCode('giftcardaccount_fixture')
    ->setStatus(Giftcardaccount::STATUS_ENABLED)
    ->setState(Giftcardaccount::STATE_AVAILABLE)
    ->setWebsiteId(Bootstrap::getObjectManager()->get(StoreManagerInterface::class)->getWebsite()->getId())
    ->setIsRedeemable(Giftcardaccount::REDEEMABLE)
    ->setBalance(8.99);

if ($fixtureData->getGiftcardaccount()) {
    $model->addData($fixtureData->getGiftcardaccount());
}

$model->save();
