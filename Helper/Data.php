<?php
/**
 * OnePica_AvaTax
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0),
 * a copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 * @copyright  Copyright (c) 2016 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 *
 * @package OnePica\AvaTax\Helper
 */
class Data extends AbstractHelper
{
    /**
     * Shipping address validation disable
     */
    const SHIPPING_ADDRESS_VALIDATION_DISABLE = 0;

    /**
     * Shipping address validation enable + prevent order
     */
    const SHIPPING_ADDRESS_VALIDATION_PREVENT = 1;

    /**
     * Shipping address validation enable + allow order
     */
    const SHIPPING_ADDRESS_VALIDATION_ALLOW = 2;

    /**
     * Message group code
     */
    const MESSAGE_GROUP_CODE = 'avatax';
}
