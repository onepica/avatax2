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
namespace Astound\AvaTax\Model\Source\Queue;

use Magento\Framework\Data\OptionSourceInterface;
use Astound\AvaTax\Model\Queue;

/**
 * Class Status
 *
 * @package Astound\AvaTax\Model\Source\Avatax16
 */
class Status implements OptionSourceInterface
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
                'value' => Queue::STATUS_PENDING ,
                'label' => __('Pending')
            ],
            [
                'value' => Queue::STATUS_RETRY,
                'label' => __('Retry pending')
            ],
            [
                'value' => Queue::STATUS_FAILED,
                'label' => __('Failed')
            ],
            [
                'value' => Queue::STATUS_COMPLETE,
                'label' => __('Complete')
            ],
            [
                'value' => Queue::STATUS_UNBALANCED,
                'label' => __('Unbalanced')
            ]
        ];
    }
}
