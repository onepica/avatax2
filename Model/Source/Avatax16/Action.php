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
 * @author     OnePica Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax\Model\Source\Avatax16;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Action
 *
 * @package OnePica\AvaTax\Model\Source\Avatax16
 */
class Action implements OptionSourceInterface
{
    /**#@+
     * Actions
     */
    const ACTION_DISABLE     = 0;
    const ACTION_CALC        = 1;
    const ACTION_CALC_SUBMIT = 2;
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
                'value' => self::ACTION_DISABLE,
                'label' => __('Disable')
            ],
            [
                'value' => self::ACTION_CALC,
                'label' => __('Enable: calculate tax')
            ],
            [
                'value' => self::ACTION_CALC_SUBMIT,
                'label' => __('Enable: calculate tax, submit data')
            ]
        ];
    }
}
