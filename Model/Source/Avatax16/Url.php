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
namespace OnePica\AvaTax\Model\Source\Avatax16;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Url
 *
 * @package OnePica\AvaTax\Model\Source\Avatax16
 */
class Url implements OptionSourceInterface
{
    /**#@+
     * Url
     */
    const DEVELOPER_URL  = 'https://tax-qa.avlr.sh/';
    const PRODUCTION_URL = 'https://tax.api.avalara.com/v2';
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
                'value' => self::PRODUCTION_URL,
                'label' => __('Production (%1)', self::PRODUCTION_URL)
            ],
            [
                'value' => self::DEVELOPER_URL,
                'label' => __('Developer (%1)', self::DEVELOPER_URL)
            ],
        ];
    }
}