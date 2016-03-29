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
use OnePica\AvaTax\Model\Service\Result\Calculation;

/**
 * Class QuoteItems
 *
 * @package OnePica\AvaTax\Model\Sales\Total\Quote
 */
class QuoteItems extends AbstractCollector
{
    /**
     * Collect quote items tax
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

        if (!$shippingAssignment->getItems()) {
            return $this;
        }

        $calculate = $this->getCalculateTool($quote, $shippingAssignment, $total);
        $result = $calculate->execute();

        if (null === $result) {
            return $this;
        }

        $store = $quote->getStore();

        /** @var \Magento\Quote\Model\Quote\Item|\Magento\Quote\Model\Quote\Address\Item $item */
        foreach ($shippingAssignment->getItems() as $item) {
            $baseTotalTax = $this->getItemTax($item, $result);
            $totalTax = $this->priceCurrency->convert($baseTotalTax);

            $itemTax = $totalTax / $item->getTotalQty();
            $baseItemTax = $baseTotalTax / $item->getTotalQty();

            $percent = $result->getItemRate($item->getId());

            $item->setTaxAmount($totalTax);
            $item->setBaseTaxAmount($baseTotalTax);

            $item->setTaxPercent($percent);

            $basePriceIncTax = $item->getBasePriceInclTax() + $baseItemTax;
            $priceInclTax = $item->getPriceInclTax() + $itemTax;

            $baseRowTotalInclTax = $item->getBaseRowTotal() + $baseTotalTax;
            $rowTotalInclTax = $item->getRowTotal() + $totalTax;

            $baseRowTotal = $item->getBaseRowTotal();
            $rowTotal = $item->getRowTotal();

            if ($this->taxDataHelper->priceIncludesTax($store)) {
                $basePriceIncTax = $item->getBasePrice();
                $priceInclTax = $item->getCalculationPrice();

                $item->setBasePrice($item->getBasePrice() - $itemTax);
                $item->setPrice($item->getPrice() - $itemTax);

                $baseRowTotalInclTax = $item->getBaseRowTotal();
                $rowTotalInclTax = $item->getRowTotal();

                $baseRowTotal = $item->getBaseRowTotal() - $baseTotalTax;
                $rowTotal = $item->getRowTotal() - $totalTax;
            }

            $item->setRowTotal($rowTotal);
            $item->setBaseRowTotal($baseRowTotal);

            $item->setBasePriceInclTax($basePriceIncTax);
            $item->setPriceInclTax($priceInclTax);

            $item->setBaseRowTotalInclTax($baseRowTotalInclTax);
            $item->setRowTotalInclTax($rowTotalInclTax);

            if (!$this->isProductCalculated($item)) {
                $total->addTotalAmount('tax', $totalTax);
                $total->addBaseTotalAmount('tax', $baseTotalTax);

                if (!$this->taxDataHelper->priceIncludesTax($store)) {
                    $total->setData('grand_total', $total->getData('grand_total') + $totalTax);
                    $total->setData('base_grand_total', $total->getData('base_grand_total') + $baseTotalTax);
                }
            }
        }

        return $this;
    }

    /**
     * Get item tax
     *
     * @param \Magento\Quote\Model\Quote\Item|\Magento\Quote\Model\Quote\Address\Item $item
     * @param Calculation                                                             $result
     * @return float
     */
    protected function getItemTax($item, $result)
    {
        if (!$this->isProductCalculated($item)) {
            return $result->getItemAmount($item->getId());
        }

        $tax = 0;
        /** @var \Magento\Quote\Model\Quote\Address\Item|\Magento\Quote\Model\Quote\Item $child */
        foreach ($item->getChildren() as $child) {
            $tax += $result->getItemAmount($child->getId());
        }

        return $tax;
    }

    /**
     * Test to see if the product carries its own numbers or is calculated based on parent or children
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return bool
     */
    public function isProductCalculated($item)
    {
        if ($item->isChildrenCalculated() && !$item->getParentItem()) {
            return true;
        }
        if (!$item->isChildrenCalculated() && $item->getParentItem()) {
            return true;
        }

        return false;
    }
}
