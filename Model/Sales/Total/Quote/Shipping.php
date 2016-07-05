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
namespace Astound\AvaTax\Model\Sales\Total\Quote;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total as AddressTotal;
use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;
use Astound\AvaTax\Model\Service\Result\Calculation;

/**
 * Class Shipping
 *
 * @package Astound\AvaTax\Model\Sales\Total\Quote
 */
class Shipping extends AbstractCollector
{
    /**
     * Collect avatax shipping tax
     *
     * @param Quote                       $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param AddressTotal                $total
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        AddressTotal $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        if ($this->isFiltered($quote, $shippingAssignment)) {
            return $this;
        }

        $result = $this->getCalculationResult($quote, $shippingAssignment, $total);

        if (null === $result) {
            return $this;
        }

        $baseTax = $result->getItemAmount($this->config->getShippingSku($quote->getStore()));
        $tax = $this->priceCurrency->convert($baseTax);

        $baseShippingAmt = $total->getBaseTotalAmount('shipping');
        $shippingAmt = $total->getTotalAmount('shipping');

        $baseShippingInclTax = $baseShippingAmt + $baseTax;
        $shippingInclTax = $shippingAmt + $tax;

        $total->setData('shipping_tax_amount', $tax);
        $total->setData('base_shipping_tax_amount', $baseTax);

        if ($this->taxDataHelper->priceIncludesTax($quote->getStore())) {
            $baseShippingInclTax = $baseShippingAmt;
            $shippingInclTax = $shippingAmt;

            $baseShippingAmt -= $baseTax;
            $shippingAmt -= $tax;

            $total->setTotalAmount('shipping', $shippingAmt);
            $total->setBaseTotalAmount('shipping', $baseShippingAmt);
        }

        $total->setData('shipping_incl_tax', $shippingInclTax);
        $total->setData('base_shipping_incl_tax', $baseShippingInclTax);

        //Add the shipping tax to total tax amount
        $total->addTotalAmount('tax', $tax);
        $total->addBaseTotalAmount('tax', $baseTax);

        if (!$this->taxDataHelper->priceIncludesTax($quote->getStore())) {
            $total->setData('grand_total', $total->getData('grand_total') + $tax);
            $total->setData('base_grand_total', $total->getData('base_grand_total') + $baseTax);
        }

        $itemsAppliedTaxes = [];
        $shippingItemSku = $this->config->getShippingSku($quote->getStore());
        $itemsAppliedTaxes[CommonTaxCollector::ITEM_TYPE_SHIPPING] = $this->getShippingItemAppliedTax(
            $result,
            $shippingItemSku
        );

        $total->setItemsAppliedTaxes($itemsAppliedTaxes);

        return $this;
    }

    /**
     * Get Shipping Item Applied Tax
     *
     * @param Calculation $result
     * @param string      $shippingItemSku
     * @return array
     */
    protected function getShippingItemAppliedTax($result, $shippingItemSku)
    {
        $jurisdictionRates = (array)$result->getItemJurisdictionRates($shippingItemSku);
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
                'item_id'     => null,
                'item_type'   => CommonTaxCollector::ITEM_TYPE_SHIPPING,
                'amount'      => $data['tax'],
                'base_amount' => $data['tax'],
                'associated_item_id' => null
            ];
        }

        return $taxGroup;
    }

    /**
     * Fetch
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param AddressTotal               $total
     * @return array|null
     */
    public function fetch(Quote $quote, AddressTotal $total)
    {
        if ($total->getData('shipping_incl_tax')) {
            return [
                'code'              => 'shipping',
                'shipping_incl_tax' => $total->getData('shipping_incl_tax')
            ];
        }

        return null;
    }
}
