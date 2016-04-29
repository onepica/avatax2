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
namespace Astound\AvaTax\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Astound\AvaTax\Setup\InstallData;

/**
 * Class QuoteSubmitBefore
 *
 * @package Astound\AvaTax\Observer
 */
class QuoteSubmitBefore implements ObserverInterface
{
    /**
     * Execute
     *
     * This is workaround for this issue
     * https://github.com/magento/magento2/issues/1616
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getData('quote');
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData('order');

        foreach ($order->getAllItems() as $item) {
            $quoteItem = $quote->getItemById($item->getQuoteItemId());
            $item->setData(
                InstallData::AVATAX_DATA_COLUMN_NAME,
                $quoteItem->getData(InstallData::AVATAX_DATA_COLUMN_NAME)
            );
        }
    }
}
