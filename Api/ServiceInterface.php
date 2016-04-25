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
namespace OnePica\AvaTax\Api;

use Magento\Framework\DataObject;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Magento\Store\Model\Store;
use OnePica\AvaTax\Model\Queue;
use OnePica\AvaTax\Model\Service\Result\ResultInterface;

/**
 * Interface ServiceInterface
 *
 * @package OnePica\AvaTax\Api
 */
interface ServiceInterface
{
    /**
     * Submit
     *
     * @param Queue $queue
     *
     * @return ResultInterface
     */
    public function submit(Queue $queue);

    /**
     * Validate
     *
     * @param DataObject $object
     * @todo need to specify which object ($object) will be passed to this method
     * @return ResultInterface
     */
    public function validate($object);

    /**
     * Creditmemo
     *
     * @param \Magento\Quote\Model\Quote                          $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total            $total
     *
     * @return \OnePica\AvaTax\Model\Service\Result\ResultInterface
     */
    public function calculate(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total);

    /**
     * Ping
     *
     * @param \Magento\Store\Model\Store $store
     * @return ResultInterface
     */
    public function ping(Store $store);
}
