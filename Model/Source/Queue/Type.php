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
namespace OnePica\AvaTax\Model\Source\Queue;

use Magento\Framework\Data\OptionSourceInterface;
use OnePica\AvaTax\Model\Queue;

/**
 * Class Type
 *
 * @package OnePica\AvaTax\Model\Source\Avatax16
 */
class Type implements OptionSourceInterface
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
                'value' => Queue::TYPE_INVOICE ,
                'label' => __('Invoice')
            ],
            [
                'value' => Queue::TYPE_CREDITMEMO,
                'label' => __('Creditmemo')
            ]
        ];
    }
}
