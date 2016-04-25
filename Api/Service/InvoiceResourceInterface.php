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
namespace OnePica\AvaTax\Api\Service;

use Magento\Sales\Model\Order\Invoice;
use OnePica\AvaTax\Api\ResultInterface;
use OnePica\AvaTax\Model\Queue;

/**
 * Interface InvoiceResourceInterface
 *
 * @package OnePica\AvaTax\Api\Service
 */
interface InvoiceResourceInterface
{
    /**
     * Get Service Request Object
     *
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $object
     * @return mixed
     */
    public function getServiceRequestObject($object);

    /**
     * Queue submit
     * Send request object to service
     *
     * @param Queue $queue
     * @return ResultInterface
     */
    public function submit(Queue $queue);
}