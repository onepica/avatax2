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
namespace OnePica\AvaTax\Model;

use Magento\Framework\Model\AbstractModel;
use OnePica\AvaTax\Api\Data\QueueInterface;
use OnePica\AvaTax\Model\ResourceModel\Queue as QueueResource;
use OnePica\AvaTax\Model\ResourceModel\Log\Collection;

/**
 * Class Queue
 *
 * @method Collection getCollection()
 * @method QueueResource getResource()
 * @method $this save()
 *
 * @package OnePica\AvaTax\Model
 */
class Queue extends AbstractModel implements QueueInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'onepica_avatax_queue';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(QueueResource::class);
    }

    /**
     * Get queue id
     *
     * @return int
     */
    public function getQueueId()
    {
        return $this->_getData(self::QUEUE_ID);
    }
    /**
     * Set queue id
     *
     * @param int $queueId
     * @return mixed
     */
    public function setQueueId($queueId)
    {
        $this->setData(self::QUEUE_ID, $queueId);

        return $this;
    }

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_getData(self::STORE_ID);
    }

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->setData(self::STORE_ID, $storeId);

        return $this;
    }

    /**
     * Get Entity Id
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->_getData(self::ENTITY_ID);
    }
    /**
     * Set Entity Id
     *
     * @param int $entityId
     * @return mixed
     */
    public function setEntityId($entityId)
    {
        $this->setData(self::ENTITY_ID, $entityId);

        return $this;
    }

    /**
     * Get Entity Increment Id
     *
     * @return string
     */
    public function getEntityIncrementId()
    {
        return $this->_getData(self::ENTITY_INCREMENT_ID);
    }

    /**
     * Set Entity Increment Id
     *
     * @param string $entityIncrementId
     * @return mixed
     */
    public function setEntityIncrementId($entityIncrementId)
    {
        $this->setData(self::ENTITY_INCREMENT_ID, $entityIncrementId);

        return $this;
    }

    /**
     * Get  type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_getData(self::TYPE);
    }

    /**
     * Set type
     *
     * @param string $type
     * @return mixed
     */
    public function setType($type)
    {
        $this->setData(self::TYPE, $type);

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->_getData(self::STATUS);
    }

    /**
     * Set status
     *
     * @param string $status
     * @return mixed
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);

        return $this;
    }

    /**
     * Get attempt
     *
     * @return string
     */
    public function getAttempt()
    {
        return $this->_getData(self::ATTEMPT);
    }

    /**
     * Set attempt
     *
     * @param string $attempt
     * @return mixed
     */
    public function setAttempt($attempt)
    {
        $this->setData(self::ATTEMPT, $attempt);

        return $this;
    }

    /**
     * Get Message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_getData(self::MESSAGE);
    }

    /**
     * Set Message
     *
     * @param string $message
     * @return mixed
     */
    public function setMessage($message)
    {
        $this->setData(self::MESSAGE, $message);

        return $this;
    }

    /**
     * Get request data
     *
     * @return string
     */
    public function getRequestData()
    {
        return $this->_getData(self::REQUEST_DATA);
    }

    /**
     * Set request data
     *
     * @param string $data
     * @return mixed
     */
    public function setRequestData($data)
    {
        $this->setData(self::REQUEST_DATA, $data);

        return $this;
    }

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(self::CREATED_AT, $createdAt);

        return $this;
    }

    /**
     * Get created at
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(self::UPDATED_AT, $updatedAt);

        return $this;
    }

    /**
     * Get queue types
     *
     * @return array
     */
    public function getAvailableQueueTypes()
    {
        return [
            self::TYPE_INVOICE    => __('Invoice'),
            self::TYPE_CREDITMEMO => __('Creditmemo')
        ];
    }

    /**
     * Get queue statuses
     *
     * @return array
     */
    public function getAvailableQueueStatuses()
    {
        return [
            self::STATUS_PENDING    => __('Pending'),
            self::STATUS_RETRY      => __('Retry pending'),
            self::STATUS_FAILED     => __('Failed'),
            self::STATUS_COMPLETE   => __('Complete'),
            self::STATUS_UNBALANCED => __('Unbalanced')
        ];
    }

    /**
     * Set entity
     *
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $object
     * @return $this
     */
    public function setEntity($object)
    {
        $this->setEntityId($object->getId());
        $this->setEntityIncrementId($object->getIncrementId());
        $this->setStoreId($object->getStoreId());

        return $this;
    }
}
