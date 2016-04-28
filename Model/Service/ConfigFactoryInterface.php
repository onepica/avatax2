<?php
/**
 * Astound_AvaTax
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0),
 * a copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Astound
 * @package    Astound_AvaTax
 * @author     Astound Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Model\Service;

use Magento\Store\Model\Store;

/**
 * Interface ConfigFactoryInterface
 *
 * @package Astound\AvaTax\Api
 */
interface ConfigFactoryInterface
{
    /**
     * Create config object
     *
     * @param \Magento\Store\Model\Store $store
     * @return \Astound\AvaTax\Model\Service\Avatax16\ConfigInterface
     */
    public function create(Store $store);
}
