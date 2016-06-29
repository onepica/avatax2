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
namespace Astound\AvaTax\Test\Integration\Helper;

use Magento\TestFramework\Helper\Bootstrap;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Astound\AvaTax\Helper\Address
     */
    protected $addressHelper;

    /**
     * Initialize necessary objects
     */
    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->addressHelper = $this->objectManager->get('Astound\AvaTax\Helper\Address');
    }

    /**
     * @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/Helper/_files/customer_address.php
     * @magentoConfigFixture current_store tax/avatax/general_group/action 2
     * @magentoConfigFixture current_store tax/avatax/region_filter_group/taxable_country CA,US
     * @magentoConfigFixture current_store tax/avatax/region_filter_group/region_filter_mode 2
     * @magentoConfigFixture current_store tax/avatax/region_filter_group/region_filter_list 12,43
     *
     * @magentoConfigFixture current_store tax/avatax/general_group/url test
     * @magentoConfigFixture current_store tax/avatax/general_group/account_number test
     * @magentoConfigFixture current_store tax/avatax/general_group/license_key test
     * @magentoConfigFixture current_store tax/avatax/general_group/company_code test
     *
     * @magentoAppIsolation enabled
     * @magentoDbIsolation  enabled
     */
    public function testIsAddressActionable()
    {
        /** @var \Magento\Framework\Registry $registry */
        $registry = $this->objectManager->get('Magento\Framework\Registry');
        $addressDefault = $registry->registry('astound_avatax_customer_address');
        $store = $this->objectManager->create('Magento\Store\Model\Store');
        $store->load(1);

        // actionable case
        $address = clone $addressDefault;
        $this->assertTrue($this->addressHelper->isAddressActionable($address, $store, null, true));

        // not actionable case
        // not taxable country
        $address = clone $addressDefault;
        $address->setCountryId('SK');
        $this->assertFalse($this->addressHelper->isAddressActionable($address, $store, null, true));

        // not actionable case
        // not actionable region
        $address = clone $addressDefault;
        $address->setRegionId(1);
        $this->assertFalse($this->addressHelper->isAddressActionable($address, $store, null, true));

        // not actionable case
        // not actionable region and region filterMode only for calculation
        $address = clone $addressDefault;
        $address->setRegionId(1);
        $filterMode = 1;
        $this->assertFalse($this->addressHelper->isAddressActionable($address, $store, $filterMode, true));
    }

    /**
     * @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/Helper/_files/invoice.php
     * @magentoConfigFixture current_store tax/avatax/general_group/action 2
     * @magentoConfigFixture current_store tax/avatax/region_filter_group/taxable_country CA,US
     * @magentoConfigFixture current_store tax/avatax/region_filter_group/region_filter_mode 2
     * @magentoConfigFixture current_store tax/avatax/region_filter_group/region_filter_list 12,43
     *
     * @magentoConfigFixture current_store tax/avatax/general_group/url test
     * @magentoConfigFixture current_store tax/avatax/general_group/account_number test
     * @magentoConfigFixture current_store tax/avatax/general_group/license_key test
     * @magentoConfigFixture current_store tax/avatax/general_group/company_code test
     *
     * @magentoAppIsolation enabled
     * @magentoDbIsolation  enabled
     */
    public function testIsObjectActionable()
    {
        /** @var \Magento\Framework\Registry $registry */
        $registry = $this->objectManager->get('Magento\Framework\Registry');
        $invoice = $registry->registry('astound_avatax_invoice');
        $address = $invoice->getShippingAddress();
        $defaultAddressData = $address->getData();

        // actionable case
        $this->assertTrue($this->addressHelper->isObjectActionable($invoice));

        // not actionable case
        // not taxable country
        $address->setCountryId('SK');
        $this->assertFalse($this->addressHelper->isObjectActionable($invoice));

        // not actionable case
        // not actionable region
        $address->setData($defaultAddressData);
        $address->setRegionId(1);
        $this->assertFalse($this->addressHelper->isObjectActionable($invoice));
    }
}
