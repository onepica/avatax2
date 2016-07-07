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
namespace Astound\AvaTax\Test\Integration\Plugin\Quote\Model\Quote;

use Magento\TestFramework\Helper\Bootstrap;

class AddressValidatorTest extends \PHPUnit_Framework_TestCase
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
     * @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/SetUpCredentials.php
     * @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/Plugin/Quote/Model/Quote/_files/quote_address.php
     * @magentoConfigFixture current_store tax/avatax/general_group/action 2
     * @magentoConfigFixture current_store tax/avatax/region_filter_group/taxable_country CA,US
     * @magentoConfigFixture current_store tax/avatax/region_filter_group/region_filter_mode 2
     * @magentoConfigFixture current_store tax/avatax/region_filter_group/region_filter_list 12,43
     *
     * @magentoAppIsolation enabled
     * @magentoDbIsolation  enabled
     */
    public function testValidate()
    {
        /** @var \Magento\Framework\Registry $registry */
        $registry = $this->objectManager->get('Magento\Framework\Registry');
        $defaultAddress = $registry->registry('astound_avatax_quote_address');

        // valid address
        $address = clone $defaultAddress;
        $this->assertTrue($address->validate());

        // invalid address, wrong postcode and street
        // should be an error
        $address = clone $defaultAddress;
        $address->setPostcode('xxxxxx');
        $address->setStreet('xxxxxx');
        $this->assertTrue(is_array($address->validate()));

        // invalid address, wrong postcode and street
        // but not actionable by country
        // should be valid
        $address = clone $defaultAddress;
        $address->setCountryId('SK');
        $this->assertTrue($address->validate());

        // invalid address, wrong postcode and street
        // but not actionable by region
        // should be valid
        $address = clone $defaultAddress;
        $address->setPostcode('xxxxxx');
        $address->setStreet('xxxxxx');
        $address->setRegionId(1);
        $this->assertTrue($address->validate());
    }
}
