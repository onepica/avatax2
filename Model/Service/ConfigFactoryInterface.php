<?php
/**
 * OnePica_AvaTax
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0),
 * a copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax\Model\Service;

use Magento\Store\Model\Store;

/**
 * Interface ConfigFactoryInterface
 *
 * @package OnePica\AvaTax\Api
 */
interface ConfigFactoryInterface
{
    /**
     * Create config object
     *
     * @param \Magento\Store\Model\Store $store
     * @return \OnePica\AvaTax\Model\Service\Avatax16\ConfigInterface
     */
    public function create(Store $store);
}
