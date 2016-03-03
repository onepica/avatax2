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
namespace OnePica\AvaTax\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ValidateAddress
 *
 * @package OnePica\AvaTax\Model\Source
 */
class ValidateAddress implements OptionSourceInterface
{
    /**#@+
     * Actions
     */
    const DISABLED              = 0;
    const ENABLED_PREVENT_ORDER = 1;
    const ENABLED_ALLOW_ORDER   = 2;
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
                'value' => self::DISABLED,
                'label' => __('Disabled')
            ],
            [
                'value' => self::ENABLED_PREVENT_ORDER,
                'label' => __('Enabled + Prevent Order')
            ],
            [
                'value' => self::ENABLED_ALLOW_ORDER,
                'label' => __('Enabled + Allow Order')
            ],
        ];
    }
}
