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
use Astound\AvaTax\Model\Log;

/**
 * Class LogType
 *
 * @package Astound\AvaTax\Model\Source\Avatax16
 */
class LogType implements OptionSourceInterface
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
                'value' => Log::PING,
                'label' => __('Ping'),
            ],
            [
                'value' => Log::CALCULATION,
                'label' => __('Calculation'),
            ],
            [
                'value' => Log::TRANSACTION,
                'label' => __('Transaction'),
            ],
            [
                'value' => Log::QUEUE,
                'label' => __('Queue'),
            ],
            [
                'value' => Log::FILTER,
                'label' => __('Filter'),
            ],
            [
                'value' => Log::VALIDATE,
                'label' => __('Validate'),
            ],

        ];
    }
}
