<?php
/**
 * OnePica_AvaTax
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0),
 * a copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 * @copyright  Copyright (c) 2016 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax\Model\Sales\Total\Quote;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;

/**
 * Class Subtotal
 *
 * @package OnePica\AvaTax\Model\Sales\Total\Quote
 */
class Subtotal extends AbstractCollector
{
    /**
     * Collect subtotal tax
     * 
     * @param \Magento\Quote\Model\Quote                          $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total            $total
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        if ($this->isProcessingSkipped($quote, $shippingAssignment)) {
            return $this;
        }

        $calculate = $this->getCalculateTool($quote, $shippingAssignment, $total);
        $result = $calculate->execute();

        if ($result === null) {
            return $this;
        }
        $baseTotalTax = 0;

        $items = $shippingAssignment->getItems();
        /** @var \Magento\Quote\Model\Quote\Item|\Magento\Quote\Model\Quote\Address\Item $item */
        foreach ($items as $item) {
            $baseTotalTax += $result->getItemAmount($item->getId());
        }

        $totalTax = $this->priceCurrency->convert($baseTotalTax);

        $subtotal = $total->getTotalAmount('subtotal');
        $baseSubtotal = $total->getBaseTotalAmount('subtotal');

        $subtotalInclTax = $total->getData('subtotal_incl_tax') + $totalTax;
        $baseSubtotalInclTax = $total->getData('base_subtotal_incl_tax') + $baseTotalTax;

        $subtotalWithDiscount = (float)$total->getSubtotalWithDiscount() + $totalTax;
        $baseSubtotalWithDiscount = (float)$total->getBaseSubtotalWithDiscount() + $baseTotalTax;

        if ($this->taxDataHelper->priceIncludesTax($quote->getStore())) {
            $subtotalInclTax = $total->getTotalAmount('subtotal');
            $baseSubtotalInclTax = $total->getBaseTotalAmount('subtotal');

            $subtotal = $total->getTotalAmount('subtotal') - $totalTax;
            $baseSubtotal = $total->getBaseTotalAmount('subtotal') - $baseTotalTax;

            $subtotalWithDiscount = (float)$total->getSubtotalWithDiscount() - $totalTax;
            $baseSubtotalWithDiscount = (float)$total->getBaseSubtotalWithDiscount() - $baseTotalTax;
        }

        //Set aggregated values
        $total->setTotalAmount('subtotal', $subtotal);
        $total->setBaseTotalAmount('subtotal', $baseSubtotal);

        $total->setSubtotalWithDiscount($subtotalWithDiscount);
        $total->setBaseSubtotalWithDiscount($baseSubtotalWithDiscount);

        $total->setData('subtotal_incl_tax', $subtotalInclTax);
        $total->setData('base_subtotal_incl_tax', $baseSubtotalInclTax);

        return $this;
    }
}
