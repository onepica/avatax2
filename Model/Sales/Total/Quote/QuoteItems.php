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

        if ($this->isFiltered($quote, $shippingAssignment)) {
            return $this;
        }

        $calculate = $this->getCalculateTool($quote, $shippingAssignment, $total);
        $result = $calculate->execute();

        if (null === $result) {
            return $this;
        }

        $store = $quote->getStore();
        $itemAppliedTax = [];
        /** @var \Magento\Quote\Model\Quote\Item|\Magento\Quote\Model\Quote\Address\Item $item */
        foreach ($shippingAssignment->getItems() as $item) {
            $baseTotalTax = $this->getItemTax($item, $result);
            $totalTax = $this->priceCurrency->convert($baseTotalTax);

            $itemTax = $totalTax / $item->getTotalQty();
            $baseItemTax = $baseTotalTax / $item->getTotalQty();

            $percent = $result->getItemRate($item->getId());

            $item->setTaxAmount($totalTax);
            $item->setBaseTaxAmount($baseTotalTax);

            $itemAppliedTax[$item->getId()] = $this->getItemAppliedTax($result, $item);

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

        $this->saveAppliedTax($result->getSummary(), $total);
        $total->setItemsAppliedTaxes($itemAppliedTax);

        return $this;
    }

    /**
     * @param Calculation                                                             $result
     * @param \Magento\Quote\Model\Quote\Item|\Magento\Quote\Model\Quote\Address\Item $item
     * @return array
     */
    protected function getItemAppliedTax($result, $item)
    {
        if ($this->isProductCalculated($item)) {
            return [];
        }

        $jurisdictionRates = (array)$result->getItemJurisdictionRates($item->getId());

        $taxGroup = [];
        foreach ($jurisdictionRates as $jurisdiction => $data) {
            $taxGroup[] = [
                'rates'       => [
                    [
                        'code'    => $jurisdiction,
                        'title'   => $jurisdiction,
                        'percent' => $data['rate']
                    ]
                ],
                'percent'     => $data['rate'],
                'id'          => $jurisdiction,
                'item_id'     => $item->getId(),
                'item_type'   => 'product',
                'amount'      => $data['tax'],
                'base_amount' => $data['tax'],
                'associated_item_id' => null
            ];
        }

        return $taxGroup;
    }

    /**
     * Save applied tax
     *
     * @param array                                    $summary
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    protected function saveAppliedTax($summary, $total)
    {
        $fullInfo = array();

        foreach ($summary as $key => $row) {
            $id = $row['name'];
            $fullInfo[$id] = [
                'rates'       => [
                    [
                        'code'     => $row['name'],
                        'title'    => $row['name'],
                        'percent'  => $row['rate'],
                    ]
                ],
                'percent'     => $row['rate'],
                'id'          => $id,
                'process'     => 0,
                'amount'      => $this->priceCurrency->convert($row['amt']),
                'base_amount' => $row['amt']
            ];
        }

        $total->setData('applied_taxes', $fullInfo);

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
