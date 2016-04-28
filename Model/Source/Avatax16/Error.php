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
 * Class Error
 *
 * @package OnePica\AvaTax\Model\Source\Avatax16
 */
class Error implements OptionSourceInterface
{
    /**#@+
     * Action on error values
     */
    const DISABLE_CHECKOUT = 1;
    const ALLOW_CHECKOUT   = 0;
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
                'value' => self::DISABLE_CHECKOUT,
                'label' => __('Disable checkout & show error message')
            ],
            [
                'value' => self::ALLOW_CHECKOUT,
                'label' => __('Allow checkout without charging tax (no error message)')
            ],
        ];
    }
}
