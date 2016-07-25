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

namespace Astound\AvaTax\Test\Integration\Model\Sales\Calculation\ExcludeTax\CartDiscount;

use Astound\AvaTax\Test\Integration\Model\Sales\Calculation\AbstractTestCase;
use \Magento\TestFramework\Helper\Bootstrap;
use \Magento\Tax\Model\Config as TaxConfig;

/**
 * Class AbstractEstimation
 *
 * @package Astound\AvaTax\Test\Integration\Model\Sales\Calculation\ExcludeTax\CartDiscount
 */
abstract class AbstractEstimation extends AbstractTestCase
{
    /**
     * Base Scenario
     *
     * @param callable|null $beforeCollectTotals
     */
    protected function scenario_x(callable $beforeCollectTotals = null)
    {
        /** @var \Magento\Framework\Registry $registry */
        $registry = $this->objectManager->get(\Magento\Framework\Registry::class);

        $fixtureData = $registry->registry("fixture");
        $expected = $registry->registry('expected');

        // check config settings
        $this->assertExpectedConfig($expected);

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->objectManager->create(\Magento\Quote\Model\Quote::class);

        $quote->load($fixtureData->getQuoteId());
        if ($beforeCollectTotals) {
            $beforeCollectTotals($quote);
        }
        $quote->collectTotals();

        //check shipping cost
        $this->assertEquals(
            $expected->getShippingCost(), $quote->getTotals()['shipping']->getValue(),
            "Shipping cost is different."
        );

        //check if cart price rule was set
        $ids = $quote->getAppliedRuleIds();
        $this->assertNotNull($ids);
        $this->assertNotEmpty($ids);

        //check tax calculated results
        $this->assertArrayHasKey('tax', $quote->getTotals(), "There is no tax totals in quote");
        $this->assertEquals(
            $expected->getTotalTax(), $quote->getTotals()['tax']->getValue(),
            "Total tax was calculated with mistake."
        );

        //check grand total calculated results
        $this->assertArrayHasKey('grand_total', $quote->getTotals(), "Grand Total is calculated with mistake.");
        $this->assertEquals(
            $expected->getGrandTotal(), $quote->getTotals()['grand_total']->getValue(),
            "Grand Total is calculated with mistake."
        );
    }
}
