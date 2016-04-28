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
 * @author     Astound Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Astound\AvaTax\Model\Sales\Total\Quote;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Tax\Helper\Data as TaxDataHelper;
use Astound\AvaTax\Helper\Address;
use Astound\AvaTax\Helper\Config;

/**
 * Class FixedTax
 *
 * @package Astound\AvaTax\Model\Sales\Total\Quote
 */
class FixedTax extends AbstractCollector
{
    /**
     * Weee data helper
     *
     * @var \Magento\Weee\Helper\Data
     */
    protected $weeeHelper;

    /**
     * FixedTax constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Registry $registry
     * @param PriceCurrencyInterface $priceCurrency
     * @param TaxDataHelper $taxDataHelper
     * @param Config $config
     * @param Address $addressHelper
     * @param \Magento\Weee\Helper\Data $weeeHelper
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        PriceCurrencyInterface $priceCurrency,
        TaxDataHelper $taxDataHelper,
        Config $config,
        Address $addressHelper,
        \Magento\Weee\Helper\Data $weeeHelper
    ) {
        parent::__construct($objectManager, $registry, $priceCurrency, $taxDataHelper, $config, $addressHelper);
        $this->weeeHelper = $weeeHelper;
    }

    /**
     * Collect fixed product tax
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
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

        $result = $this->getCalculationResult($quote, $shippingAssignment, $total);

        if (null === $result) {
            return $this;
        }
        if ($result->getHasError()) {
            return $this;
        }

        $store = $quote->getStore();

        /** @var \Magento\Quote\Model\Quote\Item|\Magento\Quote\Model\Quote\Address\Item $item */
        foreach ($shippingAssignment->getItems() as $item) {
            if ($this->isProductCalculated($item)) {
                continue;
            }

            $fptData = $result->getItemFptData($item->getId());

            if (null === $fptData) {
                continue;
            }

            $totalBaseFpt = $fptData['total_ftp_tax'];
            $totalFpt = $this->priceCurrency->convert($totalBaseFpt);

            $itemFtp = $totalFpt / $item->getTotalQty();
            $baseItemFtp = $totalBaseFpt / $item->getTotalQty();

            $this->setAppliedTax($fptData, $item);

            $item->setWeeeTaxAppliedRowAmount($totalFpt);
            $item->setBaseWeeeTaxAppliedRowAmnt($totalFpt);
            $item->setWeeeTaxAppliedAmount($itemFtp);
            $item->setBaseWeeeTaxAppliedAmount($baseItemFtp);

            if ($this->weeeHelper->includeInSubtotal($store)) {
                $total->addTotalAmount('subtotal', $totalFpt);
                $total->addBaseTotalAmount('subtotal', $totalBaseFpt);
            } else {
                $total->addTotalAmount('weee', $totalFpt);
                $total->addBaseTotalAmount('weee', $totalBaseFpt);
            }

            $total->setData('subtotal_incl_tax',$total->getData('subtotal_incl_tax') + $totalFpt);
            $total->setData('base_subtotal_incl_tax', $total->getData('base_subtotal_incl_tax') + $totalBaseFpt);

            if (!$this->taxDataHelper->priceIncludesTax($store)) {
                $total->setData('grand_total', $total->getData('grand_total') + $totalFpt);
                $total->setData('base_grand_total', $total->getData('base_grand_total') + $totalBaseFpt);
            }
        }

        return $this;
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

    /**
     * Set applied tax
     *
     * @param array $fptData
     * @param \Magento\Quote\Model\Quote\Item|\Magento\Quote\Model\Quote\Address\Item $item
     * @return array
     */
    protected function setAppliedTax(array $fptData, $item)
    {

        if (!isset($fptData['tax_details'])) {
            return $this;
        }

        $weeeTaxAppliedData = [];

        foreach ($fptData['tax_details'] as $detail) {
            $detailBaseTotalTax = $detail['tax'];
            $detailTotalTax = $this->priceCurrency->convert($detail['tax']);

            $detailBaseTax = $detailBaseTotalTax / $item->getTotalQty();
            $detailTax = $detailTotalTax / $item->getTotalQty();

            $weeeTaxAppliedData[] = [
                'title'                    => $detail['jurisdiction_name'],
                'base_amount'              => $detailBaseTax,
                'amount'                   => $detailTax,
                'row_amount'               => $detailTotalTax,
                'base_row_amount'          => $detailBaseTotalTax,
                'base_amount_incl_tax'     => $detailBaseTax,
                'amount_incl_tax'          => $detailTax,
                'row_amount_incl_tax'      => $detailTotalTax,
                'base_row_amount_incl_tax' => $detailBaseTotalTax,
            ];
        }

        $this->weeeHelper->setApplied($item, $weeeTaxAppliedData);

        return $this;
    }
}