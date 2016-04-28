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
namespace OnePica\AvaTax\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use OnePica\AvaTax\Api\Data\QueueInterface;
use OnePica\AvaTax\Api\Service\LoggerInterface;
use OnePica\AvaTax\Model\Log as LogModel;
use OnePica\AvaTax\Model\Service\Result\BaseFactory as BaseResultFactory;

/**
 * Class Queue
 *
 * @package OnePica\AvaTax\Model\ResourceModel
 */
class Queue extends AbstractDb
{
    /**
     * Saved result
     */
    const SAVED_RESULT = 'Saved';

    /**
     * Deleted result
     */
    const DELETED_RESULT = 'Deleted';

    /**
     * DateTime model
     *
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Service logger
     *
     * @var \OnePica\AvaTax\Api\Service\LoggerInterface
     */
    protected $logger;

    /**
     * Base Result Factory
     *
     * @var BaseResultFactory
     */
    protected $baseResultFactory;

    /**
     * Log constructor.
     *
     * @param Context           $context
     * @param DateTime          $dateTime
     * @param LoggerInterface   $logger
     * @param BaseResultFactory $baseResultFactory
     * @param null              $connectionName
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        LoggerInterface $logger,
        BaseResultFactory $baseResultFactory,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->dateTime = $dateTime;
        $this->logger = $logger;
        $this->baseResultFactory = $baseResultFactory;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('avatax_queue', QueueInterface::QUEUE_ID);
    }

    /**
     * Process queue data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setUpdatedAt($this->dateTime->formatDate(true));

        return parent::_beforeSave($object);
    }

    /**
     * Process queue data after saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_afterSave($object);
        $this->logAction($object, self::SAVED_RESULT);

        return $this;
    }

    /**
     * Process queue data after deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_afterDelete($object);
        $this->logAction($object, self::DELETED_RESULT);

        return $this;
    }

    /**
     * Log action
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param string $result
     * @return $this
     */
    protected function logAction(\Magento\Framework\Model\AbstractModel $object, $result)
    {
        $request = $object->toArray();
        // remove request object with credential data from log
        unset($request['request_data']);
        $baseResult = $this->baseResultFactory->create();
        $baseResult->setResponse($result);
        $this->logger->log(LogModel::QUEUE, $request, $baseResult, $object->getStoreId());

        return $this;
    }
}
