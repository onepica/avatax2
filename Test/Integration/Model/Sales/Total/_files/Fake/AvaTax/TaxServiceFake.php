<?php

namespace Fake\AvaTax;

/**
 * Class TaxServiceFake
 *
 * @package Astound\AvaTax
 */
class TaxServiceFake extends \OnePica\AvaTax16\TaxService
{
    /**
     * @param \OnePica\AvaTax16\Document\Request $documentRequest
     *
     * @return \OnePica\AvaTax16\Document\Response|void
     * @throws \Exception
     */
    public function createCalculation($documentRequest)
    {
        /** @var \Magento\Framework\Registry $registry */
        $registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\Registry');

        $registry->register('last_avatax_request', $documentRequest);

        throw new \Exception('No reposnse');
    }
}