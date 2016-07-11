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

use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$fileConfig = __DIR__.'/config.php';
$configData = include $fileConfig;

foreach ($configData as $path => $value) {
    $objectManager->get(
        'Magento\Framework\App\Config\MutableScopeConfigInterface'
    )->setValue(
        $path,
        $value,
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        0
    );
}

/** @var  \Magento\Framework\App\Config\ScopePool $scopePool */
//$scopePool = $objectManager->get(\Magento\Framework\App\Config\ScopePool::class);
//$scopePool->clean();

$test = '';
