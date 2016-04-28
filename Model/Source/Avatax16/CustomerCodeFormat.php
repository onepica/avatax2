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
 * @author     Astound Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Model\Source\Avatax16;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CustomerCodeFormat
 *
 * @package Astound\AvaTax\Model\Source\Avatax16
 */
class CustomerCodeFormat implements OptionSourceInterface
{
    /**#@+
     * Customer code format
     */
    const CUSTOMER_ID    = 1;
    const CUSTOMER_EMAIL = 2;
    /**#@-*/

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::CUSTOMER_ID,
                'label' => __('customer_id')
            ],
            [
                'value' => self::CUSTOMER_EMAIL,
                'label' => __('customer_email')
            ],
        ];
    }
}
