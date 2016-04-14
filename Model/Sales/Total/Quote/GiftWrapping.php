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
use Magento\Quote\Model\Quote\Address;
use Magento\Store\Model\Store;
use OnePica\AvaTax\Model\Service\Result\Calculation;

/**
 * Class GiftWrapping
 *
 * @package OnePica\AvaTax\Model\Sales\Total\Quote
 */
class GiftWrapping extends AbstractCollector
{
    /**
     * Collect gift wrapping tax
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

        $store = $quote->getStore();
        $result = $this->getCalculationResult($quote, $shippingAssignment, $total);
        if (null === $result) {
            return $this;
        }

        $this->applyWrappingForQuote($total, $result, $store, $quote);
        $this->applyPrintedCard($total, $result, $store, $quote);
        $this->applyGwItemsTax($total, $result, $store, $shippingAssignment);

        return $this;
    }

    /**
     * Apply wrapping tax for quote
     *
     * @param Total       $total
     * @param Calculation $result
     * @param Store       $store
     * @param Quote       $quote
     * @return $this
     */
    protected function applyWrappingForQuote($total, $result, $store, $quote)
    {
        if (!(int)$quote->getData('gw_id')) {
            return $this;
        }

        $baseTax = $result->getItemAmount($this->config->getGwOrderSku($store));
        $tax = $this->priceCurrency->convert($baseTax);

        $gwPrice = (float)$total->getData('gw_price');
        $gwBasePrice = (float)$total->getData('gw_base_price');

        $gwPriceInclTax = $gwPrice + $tax;
        $gwBasePriceInclTax = $gwBasePrice + $baseTax;

        $total->setData('gw_base_tax_amount', $baseTax);
        $total->setData('gw_tax_amount', $tax);

        if ($this->taxDataHelper->priceIncludesTax($store)) {
            $gwPriceInclTax = $gwPrice;
            $gwBasePriceInclTax = $gwBasePrice;

            $total->setData('gw_price', $gwPrice - $tax);
            $total->setData('gw_base_price', $gwBasePrice - $baseTax);
        }

        $total->setData('gw_price_incl_tax', $gwPriceInclTax);
        $total->setData('gw_base_price_incl_tax', $gwBasePriceInclTax);

        $total->addTotalAmount('tax', $tax);
        $total->addBaseTotalAmount('tax', $baseTax);

        if (!$this->taxDataHelper->priceIncludesTax($quote->getStore())) {
            $total->setData('grand_total', $total->getData('grand_total') + $tax);
            $total->setData('base_grand_total', $total->getData('base_grand_total') + $baseTax);
        }

        return $this;
    }

    /**
     * Apply printed card tax
     *
     * @param Total       $total
     * @param Calculation $result
     * @param Store       $store
     * @param Quote       $quote
     * @return $this
     */
    protected function applyPrintedCard($total, $result, $store, $quote)
    {
        if (!(int)$quote->getData('gw_add_card')) {
            return $this;
        }

        $baseTax = $baseTax = $result->getItemAmount($this->config->getGwPrintedCardSku($store));
        $tax = $this->priceCurrency->convert($baseTax);

        $gwPrice = (float)$total->getData('gw_card_price');
        $gwBasePrice = (float)$total->getData('gw_card_base_price');

        $gwPriceInclTax = $gwPrice + $tax;
        $gwBasePriceInclTax = $gwBasePrice + $baseTax;

        $total->setData('gw_card_base_tax_amount', $baseTax);
        $total->setData('gw_card_tax_amount', $tax);

        if ($this->taxDataHelper->priceIncludesTax($store)) {
            $gwPriceInclTax = $gwPrice;
            $gwBasePriceInclTax = $gwBasePrice;

            $total->setData('gw_card_price', $gwPrice - $tax);
            $total->setData('gw_card_base_price', $gwBasePrice - $baseTax);
        }

        $total->setData('gw_card_price_incl_tax', $gwPriceInclTax);
        $total->setData('gw_card_base_price_incl_tax', $gwBasePriceInclTax);

        $total->addTotalAmount('tax', $tax);
        $total->addBaseTotalAmount('tax', $baseTax);

        if (!$this->taxDataHelper->priceIncludesTax($quote->getStore())) {
            $total->setData('grand_total', $total->getData('grand_total') + $tax);
            $total->setData('base_grand_total', $total->getData('base_grand_total') + $baseTax);
        }

        return $this;
    }

    /**
     * Apply gw items tax
     *
     * @param Total                       $total
     * @param Calculation                 $result
     * @param Store                       $store
     * @param ShippingAssignmentInterface $shippingAssignment
     * @return $this
     */
    protected function applyGwItemsTax($total, $result, $store, $shippingAssignment)
    {
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($shippingAssignment->getItems() as $item) {
            if ($item->getProduct()->isVirtual() || $item->getParentItem() || !$item->getGwId()) {
                continue;
            }

            $baseTotalTax = $result->getGwItemAmount($item->getId());
            $totalTax = $this->priceCurrency->convert($baseTotalTax);

            $baseItemTax = $baseTotalTax / $item->getQty();
            $itemTax = $this->priceCurrency->convert($baseItemTax);

            $itemsPriceInclTax = $total->getData('gw_items_price') + $totalTax;
            $itemsBasePriceInclTax = $total->getData('gw_items_base_price') + $baseTotalTax;

            $item->setData('gw_base_tax_amount', $baseItemTax);
            $item->setData('gw_tax_amount', $itemTax);
            $item->setTaxPercent($result->getItemRate($item->getId()));

            $total->setData('gw_items_base_tax_amount', $total->getData('gw_items_base_tax_amount') + $baseTotalTax);
            $total->setData('gw_items_tax_amount', $total->getData('gw_items_tax_amount') + $totalTax);

            if ($this->taxDataHelper->priceIncludesTax($store)) {
                $itemsPriceInclTax = $total->getData('gw_items_price');
                $itemsBasePriceInclTax = $total->getData('gw_items_base_price');

                $total->setData('gw_items_price', $itemsPriceInclTax - $totalTax);
                $total->setData('gw_items_base_price', $itemsBasePriceInclTax - $baseTotalTax);

                $item->setData('gw_base_price', $item->getData('gw_base_price') - $baseItemTax);
                $item->setData('gw_price', $item->getData('gw_price') - $itemTax);
            }

            $total->setData('gw_items_price_incl_tax', $itemsPriceInclTax);
            $total->setData('gw_items_base_price_incl_tax', $itemsBasePriceInclTax);

            $total->addTotalAmount('tax', $totalTax);
            $total->addBaseTotalAmount('tax', $baseTotalTax);

            if (!$this->taxDataHelper->priceIncludesTax($store)) {
                $total->setData('grand_total', $total->getData('grand_total') + $itemTax);
                $total->setData('base_grand_total', $total->getData('base_grand_total') + $baseItemTax);
            }
        }

        return $this;
    }
}
