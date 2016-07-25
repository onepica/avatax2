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

namespace Astound\AvaTax\Test\Integration\Model\Sales\Calculation\IncludeTax\GiftCard;

use Astound\AvaTax\Test\Integration\Model\Sales\Calculation\AbstractTestCase;

/**
 * Class Estimation
 *
 * @package Astound\AvaTax\Test\Integration\Model\Sales\Calculation\IncludeTax\GiftCard
 */
class Estimation extends AbstractTestCase
{
    /**
     * Scenario: Client estimate tax in cart
     *  Given cart contains 1 simple product
     *  And product price is 100
     *  And product qty is 1
     *  And flat shipping price is 10
     *  And shipping address is "US, NewYork, NewYork, 10038"
     *  And Tax Calculation Settings/Catalog Prices : Exclude Tax
     *  Then Tax rate should be 0.08875
     *  And Total tax should be 8.97
     *  And Shipping cost is 9.18
     *  And Gift Card Account Amount is -9.99
     *  And Grand total is 100.01
     *
     *  @magentoConfigFixture default_store tax/calculation/price_includes_tax 1
     *  @magentoConfigFixture default_store carriers/flatrate/price 10
     *  @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/Model/Sales/Calculation/_files/scenario/data/init_avatax.php
     *  @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/Model/Sales/Calculation/IncludeTax/GiftCard/_files/scenario/data/scenario_001.php
     *  @magentoDbIsolation enabled
     *  @test
     */
    public function scenario_001()
    {
        /** @var \Magento\Framework\Registry $registry */
        $registry = $this->objectManager->get(\Magento\Framework\Registry::class);
        $fixtureData = $registry->registry("fixture");
        $expected = $registry->registry('expected');

        //check config settings
        $this->assertExpectedConfig($expected);

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->objectManager->create(\Magento\Quote\Model\Quote::class);

        $quote->load($fixtureData->getQuoteId());
        $quote->collectTotals();

        //check shipping cost
        $this->assertEquals(
            $expected->getShippingCost(), $quote->getTotals()['shipping']->getValue(),
            "Shipping cost is different."
        );

        //check giftcardaccount
        $this->assertArrayHasKey(
            'giftcardaccount',
            $quote->getTotals(),
            "There is no gift card account totals in quote"
        );
        $this->assertEquals(
            $expected->getGiftcardaccountAmount(), $quote->getTotals()['giftcardaccount']->getValue(),
            "Total gift card amount was calculated with mistake"
        );

        //check tax calculated results
        $this->assertArrayHasKey('tax', $quote->getTotals(), "There is no tax totals in quote");
        $this->assertEquals(
            $expected->getTotalTax(), $quote->getTotals()['tax']->getValue(),
            "Total tax was calculated with mistake"
        );

        //check grand total calculated results
        $this->assertArrayHasKey('grand_total', $quote->getTotals(), "Grand Total is calculated with mistake.");
        $this->assertEquals(
            $expected->getGrandTotal(), $quote->getTotals()['grand_total']->getValue(),
            "Grand Total is calculated with mistake."
        );
    }
}
