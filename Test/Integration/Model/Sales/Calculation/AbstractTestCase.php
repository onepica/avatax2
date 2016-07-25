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

namespace Astound\AvaTax\Test\Integration\Model\Sales\Calculation;

use \Magento\TestFramework\Helper\Bootstrap;

use \Magento\Framework\App\Config\MutableScopeConfigInterface;
use \Magento\Store\Model\ScopeInterface;
use \Magento\Framework\DataObject;
use \Magento\Store\Model\StoreManagerInterface;
use \Astound\AvaTax\Model\Service\Avatax16;

/**
 * Class AbstractEstimation
 *
 * @package Astound\AvaTax\Test\Integration\Model\Sales\Calculation\ExcludeTax
 */
abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $objectManager;

    /**
     *
     * @throws \Exception
     */
    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->assertService();
    }

    /**
     * Check if Server is available with given credentials
     *
     * @throws \Exception
     */
    protected function assertService()
    {
        /** @var \Magento\Store\Model\StoreManagerInterface $sm */
        $sm = $this->objectManager->get(StoreManagerInterface::class);
        $store = $sm->getStore('default');

        /** @var \Astound\AvaTax\Model\Service\Avatax16 $service */
        $service = $this->objectManager->create(Avatax16::class);
        $pingResult = $service->ping($store);
        if ($pingResult->getHasError()) {
            throw new \Exception(
                'Service ping request returns error : '
                . $pingResult->getErrorsAsString()
            );
        }
    }

    /**
     * Assert Expected Config
     *
     * @param \Magento\Framework\DataObject $expected
     */
    protected function assertExpectedConfig(DataObject $expected)
    {
        $expectedConfig = $expected->getConfig();
        if ($expectedConfig) {
            /** @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig */
            $scopeConfig = $this->objectManager->get(MutableScopeConfigInterface::class);
            $expectedStoreConfig = $expectedConfig['store'];
            if ($expectedStoreConfig) {
                foreach ($expectedStoreConfig as $store => $config) {
                    foreach ($config as $path => $expectedValue) {
                        $actualValue = $scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $store);
                        $this->assertEquals(
                            $expectedValue, $actualValue,
                            "Configured value of $path is different"
                        );
                    }
                }
            }
        }
    }
}
