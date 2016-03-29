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
    const ENTITY_ID           = 'entity_id';
    const ENTITY_INCREMENT_ID = 'entity_increment_id';
    const TYPE                = 'type';
    const STATUS              = 'status';
    const ATTEMPT             = 'attempt';
    const MESSAGE             = 'message';
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
     * Get Entity Id
     *
     * @return int
     */
    public function getEntityId();

    /**
     * Set Entity Id
     *
     * @param int $entityId
     * @return mixed
     */
    public function setEntityId($entityId);


    /**
     * Get Entity Increment Id
     *
     * @return string
     */
    public function getEntityIncrementId();

    /**
     * Set Entity Increment Id
     *
     * @param string $entityIncrementId
     * @return mixed
     */
    public function setEntityIncrementId($entityIncrementId);

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
     * @return string
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
     * @return string
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
     * @return string
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
     * @return string
     */
    public function setMessage($message);

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
     * @return string
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
     * @return string
     */
    public function setUpdatedAt($updatedAt);
}
