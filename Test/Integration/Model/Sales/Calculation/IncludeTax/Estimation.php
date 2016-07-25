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

namespace Astound\AvaTax\Test\Integration\Model\Sales\Calculation\IncludeTax;

/**
 * Class Estimation
 *
 * @package Astound\AvaTax\Test\Integration\Model\Sales\Calculation\IncludeTax
 */
class Estimation extends AbstractEstimation
{
    /**
     * Scenario: Client estimate tax in cart
     *  Given cart contains 1 simple product
     *  And product price is 100
     *  And product qty is 1
     *  And flat shipping price is 10
     *  And shipping address is "US, NewYork, NewYork, 10118"
     *  And Tax Calculation Settings/Catalog Prices : Included Tax
     *  Then Tax rate should be 0.08875
     *  And Total tax should be 8.97
     *  And Shipping cost should be 9.18
     *  And Grand total should be 110
     *
     *  @magentoConfigFixture default_store tax/calculation/price_includes_tax 1
     *  @magentoConfigFixture default_store carriers/flatrate/price 10
     *  @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/_files/scenario/setup_credentials.php
     *  @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/Model/Sales/Calculation/IncludeTax/_files/scenario/data/scenario_001.php
     *  @magentoDbIsolation enabled
     *  @test
     */
    public function scenario_001()
    {
        parent::scenario_x();
    }

    /**
     * Scenario: Client estimate tax in cart
     *  Given cart contains 2 simple product
     *  And product price is 100
     *  And product qty is 2
     *  And flat shipping price is 10
     *  And shipping address is "US, NewYork, NewYork, 10118"
     *  And Tax Calculation Settings/Catalog Prices : Included Tax
     *  Then Tax rate should be 0.08875
     *  And Total tax should be 17.93
     *  And Shipping cost should be 18.37
     *  And Grand total should be 220
     *
     *  @magentoConfigFixture default_store tax/calculation/price_includes_tax 1
     *  @magentoConfigFixture default_store carriers/flatrate/price 10
     *  @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/_files/scenario/setup_credentials.php
     *  @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/Model/Sales/Calculation/IncludeTax/_files/scenario/data/scenario_002.php
     *  @magentoDbIsolation enabled
     *  @test
     */
    public function scenario_002()
    {
        parent::scenario_x();
    }
}
