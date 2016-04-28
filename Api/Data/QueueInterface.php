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
namespace OnePica\AvaTax\Api\Data;

/**
 * Interface QueueInterface
 *
 * @package OnePica\AvaTax\Api\Data
 */
interface QueueInterface
{
    /**
     * Can add to queue flag
     */
    const FLAG_CAN_ADD_TO_QUEUE = 'avatax_can_add_to_queue';

    /**
     * Queue Type
     */
    const TYPE_INVOICE     = 'Invoice';
    const TYPE_CREDITMEMO  = 'Credit memo';
    /**#@-*/

    /**
     * Queue Status
     */
    const STATUS_PENDING    = 'Pending';
    const STATUS_RETRY      = 'Retry pending';
    const STATUS_FAILED     = 'Failed';
    const STATUS_COMPLETE   = 'Complete';
    const STATUS_UNBALANCED = 'Unbalanced';
    /**#@-*/

    /**#@+
     * Constants defined for keys of array
     */
    const QUEUE_ID            = 'queue_id';
    const STORE_ID            = 'store_id';
    const ORDER_ID            = 'order_id';
    const OBJECT_ID           = 'object_id';
    const OBJECT_INCREMENT_ID = 'object_increment_id';
    const TYPE                = 'type';
    const STATUS              = 'status';
    const ATTEMPT             = 'attempt';
    const MESSAGE             = 'message';
    const REQUEST_DATA        = 'request_data';
    const TOTAL_TAX_AMOUNT    = 'total_tax_amount';
    const CREATED_AT          = 'created_at';
    const UPDATED_AT          = 'updated_at';
    /**#@-*/

    /**
     * Get queue id
     *
     * @return int
     */
    public function getQueueId();

    /**
     * Set queue id
     *
     * @param int $queueId
     * @return mixed
     */
    public function setQueueId($queueId);

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set store id
     *
     * @param int $storeId
     * @return mixed
     */
    public function setStoreId($storeId);

    /**
     * Get Order Id
     *
     * @return int
     */
    public function getOrderId();

    /**
     * Set Order Id
     *
     * @param int $orderId
     *
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Get Object Increment Id
     *
     * @return string
     */
    public function getObjectIncrementId();

    /**
     * Set Object Increment Id
     *
     * @param string $entityIncrementId
     * @return string
     */
    public function setObjectIncrementId($entityIncrementId);

    /**
     * Get  type
     *
     * @return string
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return mixed
     */
    public function setType($type);

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return mixed
     */
    public function setStatus($status);

    /**
     * Get attempt
     *
     * @return string
     */
    public function getAttempt();

    /**
     * Set attempt
     *
     * @param string $attempt
     * @return mixed
     */
    public function setAttempt($attempt);

    /**
     * Get Message
     *
     * @return string
     */
    public function getMessage();

    /**
     * Set Message
     *
     * @param string $message
     * @return mixed
     */
    public function setMessage($message);

    /**
     * Get request data
     *
     * @return string
     */
    public function getRequestData();

    /**
     * Set request data
     *
     * @param string $data
     * @return mixed
     */
    public function setRequestData($data);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return mixed
     */
    public function setCreatedAt($createdAt);

    /**
     * Get created at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set created at
     *
     * @param string $updatedAt
     * @return mixed
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Set entity
     *
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $object
     * @return $this
     */
    public function setEntity($object);

    /**
     * Total tax amount
     *
     * @return float
     */
    public function getTotalTaxAmount();

    /**
     * Set total tax amount
     *
     * @param float $totalTaxAmount
     * @return $this
     */
    public function setTotalTaxAmount($totalTaxAmount);

    /**
     * Get Object Id
     *
     * @return int
     */
    public function getObjectId();

    /**
     * Set Object Id
     *
     * @param int $objectId
     *
     * @return $this
     */
    public function setObjectId($objectId);
}
