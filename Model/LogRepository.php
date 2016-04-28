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
namespace OnePica\AvaTax\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use OnePica\AvaTax\Api\Data;
use OnePica\AvaTax\Api\LogRepositoryInterface;

/**
 * Class LogRepository
 *
 * @package OnePica\AvaTax\Model
 */
class LogRepository implements LogRepositoryInterface
{
    /**
     * Log factory
     *
     * @var \OnePica\AvaTax\Model\LogFactory
     */
    protected $logFactory;

    /**
     * Log resource model
     *
     * @var \OnePica\AvaTax\Model\ResourceModel\Log
     */
    protected $logResource;

    /**
     * LogRepository constructor.
     *
     * @param \OnePica\AvaTax\Model\LogFactory        $logFactory
     * @param \OnePica\AvaTax\Model\ResourceModel\Log $logResource
     */
    public function __construct(LogFactory $logFactory, ResourceModel\Log $logResource)
    {
        $this->logFactory = $logFactory;
        $this->logResource = $logResource;
    }

    /**
     * Save log
     *
     * @param \OnePica\AvaTax\Api\Data\LogInterface $log
     * @return \OnePica\AvaTax\Api\Data\LogInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(Data\LogInterface $log)
    {
        try {
            $this->logResource->save($log);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $log;
    }

    /**
     * Retrieve log.
     *
     * @param int $logId
     * @return \OnePica\AvaTax\Api\Data\LogInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($logId)
    {
        $log = $this->logFactory->create();
        $this->logResource->load($log, $logId);
        if (!$log->getId()) {
            throw new NoSuchEntityException(__('Avatax Log with id "%1" does not exist.', $logId));
        }

        return $log;
    }
}
