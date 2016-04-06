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

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Tax\Helper\Data as TaxDataHelper;
use OnePica\AvaTax\Helper\Config;

/**
 * Class FixedTax
 * @package OnePica\AvaTax\Model\Sales\Total\Quote
 */
class FixedTax extends AbstractCollector
{
    /**
     * @var \Magento\Weee\Helper\Data
     */
    private $weeeHelper;

    /**
     * FixedTax constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Registry $registry
     * @param PriceCurrencyInterface $priceCurrency
     * @param TaxDataHelper $taxDataHelper
     * @param Config $config
     * @param \Magento\Weee\Helper\Data $weeeHelper
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        PriceCurrencyInterface $priceCurrency,
        TaxDataHelper $taxDataHelper,
        Config $config,
        \Magento\Weee\Helper\Data $weeeHelper
    ) {
        parent::__construct($objectManager, $registry, $priceCurrency, $taxDataHelper, $config);
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
            if ($this->isProductCalculated($item)) {
                continue;
            }

            $fptData = $result->getItemFptData($item->getId());
            $totalBaseFpt = $fptData['total_ftp_tax'];
            $totalFpt = $this->priceCurrency->convert($totalBaseFpt);

            $itemFtp = $totalFpt / $item->getTotalQty();
            $baseItemFtp = $totalBaseFpt / $item->getTotalQty();

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

            $item->setWeeeTaxAppliedRowAmount($totalFpt);
            $item->setBaseWeeeTaxAppliedRowAmnt($totalFpt);
            $item->setWeeeTaxAppliedAmount($itemFtp);
            $item->setBaseWeeeTaxAppliedAmount($baseItemFtp);

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
}