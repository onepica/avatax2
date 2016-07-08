<?php
use Magento\Tax\Model\Config;
use Astound\AvaTax\Helper\Config as AvaTaxConfig;

$configData = array(
    AvaTaxConfig::AVATAX_SERVICE_URL => 'https://tax-qa.avlr.sh/v2',
    AvaTaxConfig::AVATAX_SERVICE_ACCOUNT_NUMBER => '12213ceb-ccaa-4858-9e10-32246e81a546',
    AvaTaxConfig::AVATAX_SERVICE_LICENCE_KEY => 'RsEEliKdpUajNWh5BN6P4RYRQR83QuL5X0dI4U5Zk2Vay/awKJ9gbFJwHn+i1Lft',
    AvaTaxConfig::AVATAX_SERVICE_COMPANY_CODE => 'ASTOUND',
);

foreach ($configData as $path => $value) {
    \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
        'Magento\Framework\App\Config\MutableScopeConfigInterface'
    )->setValue(
            $path,
            $value,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            0
        );
}
