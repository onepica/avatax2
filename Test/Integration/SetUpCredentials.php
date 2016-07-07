<?php
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Tax\Model\Config;
use Astound\AvaTax\Helper\Config as AvaTaxConfig;

$configData = array(
    AvaTaxConfig::AVATAX_SERVICE_URL => 'XXXXXXX',
    AvaTaxConfig::AVATAX_SERVICE_ACCOUNT_NUMBER => 'XXXXXXX',
    AvaTaxConfig::AVATAX_SERVICE_LICENCE_KEY => 'XXXXXXX',
    AvaTaxConfig::AVATAX_SERVICE_COMPANY_CODE => 'XXXXXXX',
);

$objectManager = Bootstrap::getObjectManager();

/** @var \Magento\Config\Model\ResourceModel\Config $config */
$config = $objectManager->get('Magento\Config\Model\ResourceModel\Config');
foreach ($configData as $path => $value) {
    $config->saveConfig(
        $path,
        $value,
        ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        0
    );
}
