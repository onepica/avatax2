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
namespace Astound\AvaTax\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Astound\AvaTax\Helper\Data as AvataxDataHelper;

/**
 * Class ValidateAddress
 *
 * @package Astound\AvaTax\Model\Source
 */
class ValidateAddress implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => AvataxDataHelper::SHIPPING_ADDRESS_VALIDATION_DISABLE,
                'label' => __('Disable')
            ],
            [
                'value' => AvataxDataHelper::SHIPPING_ADDRESS_VALIDATION_PREVENT,
                'label' => __('Enable + Prevent Order')
            ],
            [
                'value' => AvataxDataHelper::SHIPPING_ADDRESS_VALIDATION_ALLOW,
                'label' => __('Enable + Allow Order')
            ],
        ];
    }
}
