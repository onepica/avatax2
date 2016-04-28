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
namespace Astound\AvaTax\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 *
 * @package Astound\AvaTax\Helper
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
     * Region filter disable mode
     */
    const REGION_FILTER_MODE_OFF = 0;

    /**
     * Region filter tax mode
     */
    const REGION_FILTER_MODE_TAX = 1;

    /**
     * Region filter all mode
     */
    const REGION_FILTER_MODE_ALL = 2;

    /**
     * Message group code
     */
    const MESSAGE_GROUP_CODE = 'avatax';

    /**
     * Adds a comment to order history.
     *
     * @param \Magento\Sales\Model\Order $order
     * @param string                     $comment
     * @return $this
     */
    public function addStatusHistoryCommentToOrder($order, $comment)
    {
        $order->addStatusHistoryComment($comment, $order->getStatus())
            ->save();

        return $this;
    }
}
