<?php
use Magento\Tax\Model\Config;

$configData = include __DIR__ . '/credentials.php';

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
