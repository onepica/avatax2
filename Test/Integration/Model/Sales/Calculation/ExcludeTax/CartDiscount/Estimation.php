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

/**
 * Class Estimation
 *
 * @package Astound\AvaTax\Test\Integration\Model\Sales\Calculation\ExcludeTax\CartDiscount
 */
class Estimation extends AbstractEstimation
{
    /**
     * Scenario: Client estimate tax in cart
     *  Given cart contains 1 simple product
     *  And product price is 100
     *  And product qty is 1
     *  And flat shipping price is 10
     *  And shipping address is "US, NewYork, NewYork, 10038"
     *  And rate discount on subtotal is 20%
     *  And Tax Calculation Settings/Catalog Prices : Exclude Tax
     *  And Tax Calculation Settings/Discount : Apply After
     *  Then Tax rate should be 0.08875
     *  And Total tax should be 7.99
     *  And Shipping cost is 10
     *  And Grand Total is 97.99
     *
     *  @magentoConfigFixture default_store tax/calculation/price_includes_tax 0
     *  @magentoConfigFixture default_store tax/calculation/apply_after_discount 1
     *  @magentoConfigFixture default_store carriers/flatrate/price 10
     *  @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/Model/Sales/Calculation/_files/scenario/data/init_avatax.php
     *  @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/Model/Sales/Calculation/ExcludeTax/CartDiscount/_files/scenario/data/scenario_001.php
     *  @magentoDbIsolation enabled
     *  @test
     */
    public function scenario_001()
    {
        parent::scenario_x();
    }

    /**
     * Scenario: Client estimate tax in cart
     *  Given cart contains 1 simple product
     *  And product price is 100
     *  And product qty is 1
     *  And flat shipping price is 10
     *  And shipping address is "US, NewYork, NewYork, 10038"
     *  And rate discount on subtotal with coupon is 10%
     *  And Tax Calculation Settings/Catalog Prices : Exclude Tax
     *  And Tax Calculation Settings/Discount : Apply After
     *  Then Tax rate should be 0.08875
     *  And Total tax should be 8.88
     *  And Shipping cost is 10
     *  And Grand Total is 108.88
     *
     *  @magentoConfigFixture default_store tax/calculation/price_includes_tax 0
     *  @magentoConfigFixture default_store tax/calculation/apply_after_discount 1
     *  @magentoConfigFixture default_store carriers/flatrate/price 10
     *  @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/Model/Sales/Calculation/_files/scenario/data/init_avatax.php
     *  @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/Model/Sales/Calculation/ExcludeTax/CartDiscount/_files/scenario/data/scenario_002.php
     *  @magentoDbIsolation enabled
     *  @test
     */
    public function scenario_002()
    {
        parent::scenario_x(
            function ($quote) {
                $quote->setCouponCode('10OFF');
                $quote->save();
            }
        );
    }

    /**
     * Scenario: Client estimate tax in cart
     *  Given cart contains 1 simple product
     *  And product price is 100
     *  And product qty is 1
     *  And flat shipping price is 10
     *  And shipping address is "US, NewYork, NewYork, 10038"
     *  And rate discount on subtotal is 20%
     *  And Tax Calculation Settings/Catalog Prices : Exclude Tax
     *  And Tax Calculation Settings/Discount : Apply Before
     *  Then Tax Calculation Settings/Discount is behave like Apply After
     *  And Calculation results should be the same as in scenario_001
     *  And Tax rate should be 0.08875
     *  And Total tax should be 7.99
     *  And Shipping cost is 10
     *  And Grand total is 97.99
     *
     *  @magentoConfigFixture default_store tax/calculation/price_includes_tax 0
     *  @magentoConfigFixture default_store tax/calculation/apply_after_discount 0
     *  @magentoConfigFixture default_store carriers/flatrate/price 10
     *  @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/Model/Sales/Calculation/_files/scenario/data/init_avatax.php
     *  @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/Model/Sales/Calculation/ExcludeTax/CartDiscount/_files/scenario/data/scenario_003.php
     *  @magentoDbIsolation enabled
     *  @test
     */
    public function scenario_003()
    {
        parent::scenario_x();
    }

    /**
     * Scenario: Client estimate tax in cart
     *  Given cart contains 1 simple product
     *  And product price is 100
     *  And product qty is 1
     *  And flat shipping price is 10
     *  And shipping address is "US, NewYork, NewYork, 10038"
     *  And rate discount on subtotal is 10%
     *  And Tax Calculation Settings/Catalog Prices : Exclude Tax
     *  And Tax Calculation Settings/Discount : Apply Before
     *  Then Tax Calculation Settings/Discount is behave like Apply After
     *  And Calculation results should be the same as in scenario_002
     *  And Tax rate should be 0.08875
     *  And Total tax should be 8.88
     *  And Shipping cost is 10
     *  And Grand total is 108.88
     *
     *  @magentoConfigFixture default_store tax/calculation/price_includes_tax 0
     *  @magentoConfigFixture default_store tax/calculation/apply_after_discount 0
     *  @magentoConfigFixture default_store carriers/flatrate/price 10
     *  @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/Model/Sales/Calculation/_files/scenario/data/init_avatax.php
     *  @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/Model/Sales/Calculation/ExcludeTax/CartDiscount/_files/scenario/data/scenario_004.php
     *  @magentoDbIsolation enabled
     *  @test
     */
    public function scenario_004()
    {
        parent::scenario_x(
            function ($quote) {
                $quote->setCouponCode('10OFF');
                $quote->save();
            }
        );
    }
}
