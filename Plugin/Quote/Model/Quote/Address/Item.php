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

namespace Astound\AvaTax\Plugin\Quote\Model\Quote\Address;

/**
 * Class Item
 * 
 * @package Astound\AvaTax\Plugin\Quote\Model\Quote\Address
 */
class Item
{
    /**
     * Set avatax data to address item
     *
     * @param \Magento\Quote\Model\Quote\Address\Item $subject
     * @param \Closure                                $proceed
     * @param \Magento\Quote\Model\Quote\Item         $quoteItem
     * @return \Magento\Quote\Model\Quote\Address\Item
     */
    public function aroundImportQuoteItem(
        \Magento\Quote\Model\Quote\Address\Item $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item $quoteItem
    ) {
        $proceed($quoteItem);
        $subject->setData('avatax_data', $quoteItem->getData('avatax_data'));

        return $subject;
    }
}