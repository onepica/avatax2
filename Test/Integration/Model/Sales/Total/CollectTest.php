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
namespace Astound\AvaTax\Test\Integration\Model\Sales\Total;

use Fake\AvaTax\Model\Service\Avatax16\ConfigFake;
use Fake\AvaTax\TaxServiceFake;

/**
 * Class Collect
 *
 * @package Astound\AvaTax\Test\Integration\Model\Sales\Total
 */
class CollectTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    private $objectManager;

    /**
     * @throws \Exception
     */
    protected function setUp()
    {
        $autoloadWrapper = \Magento\Framework\Autoload\AutoloaderRegistry::getAutoloader();
        $autoloadWrapper->addPsr4('Fake\\AvaTax\\', realpath(__DIR__ . '/_files/Fake/AvaTax'));

        $autoloadWrapper->findFile(ConfigFake::class);
        $autoloadWrapper->findFile(TaxServiceFake::class);

        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->objectManager->configure(
            [
                'preferences' => [
                    \Astound\AvaTax\Model\Service\Avatax16\Config::class => ConfigFake::class,
                ],
            ]
        );
    }

    /**
     * This test check, that request which will be sent to avalara, was built as we expect.
     *
     * @magentoConfigFixture current_store tax/avatax/general_group/action 2
     * @magentoConfigFixture current_store tax/avatax/general_group/account_number test_account_id
     * @magentoConfigFixture current_store tax/avatax/general_group/company_code company_code
     * @magentoConfigFixture current_store tax/avatax/general_group/url http://some.url
     * @magentoDataFixture   ../../../../app/code/Astound/AvaTax/Test/Integration/_files/checkRequestTest.php
     * @magentoDbIsolation   enabled
     * @magentoAppIsolation  enabled
     * @test
     */
    public function checkRequest()
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->objectManager->create('Magento\Quote\Model\Quote');

        /** @var \Magento\Framework\Registry $registry */
        $registry = $this->objectManager->get('Magento\Framework\Registry');

        $quote->load($registry->registry('astound_avatax_quote_id'));
        $quote->collectTotals();

        //todo
        //var_dump($registry->registry('last_avatax_request')->toArray());
    }


    /**
     * Case when avatax lib throw exception
     *
     * @test
     */
    public function exceptionResponse()
    {
    }
}
