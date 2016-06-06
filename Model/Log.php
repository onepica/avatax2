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
 * @author     Astound Codemaster <codemaster@astoundcommerce.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Model;

use Magento\Framework\Model\AbstractModel;
use Astound\AvaTax\Api\Data\LogInterface;
use Astound\AvaTax\Model\ResourceModel\Log as LogResource;
use Astound\AvaTax\Model\ResourceModel\Log\Collection;

/**
 * Class Log
 *
 * @method Collection getCollection()
 * @method LogResource getResource()
 * @method $this save()
 *
 * @package Astound\AvaTax\Model
 */
class Log extends AbstractModel implements LogInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'astound_avatax_log';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(LogResource::class);
    }

    /**
     * Get log id
     * 
     * @return int
     */
    public function getLogId()
    {
        return $this->getId();
    }

    /**
     * Set log id
     * 
     * @param int $id
     *
     * @return $this
     */
    public function setLogId($id)
    {
        $this->setId($id);

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
     * Get log level
     *
     * @return string
     */
    public function getLogLevel()
    {
        return $this->_getData(self::LOG_LEVEL);
    }

    /**
     * Set log level
     *
     * @param string $logLevel
     * @return $this
     */
    public function setLogLevel($logLevel)
    {
        $this->setData(self::LOG_LEVEL, $logLevel);

        return $this;
    }

    /**
     * Get log type
     *
     * @return string
     */
    public function getLogType()
    {
        return $this->_getData(self::LOG_TYPE);
    }

    /**
     * Set log type
     *
     * @param string $logType
     * @return $this
     */
    public function setLogType($logType)
    {
        $this->setData(self::LOG_TYPE, $logType);

        return $this;
    }

    /**
     * Get request
     *
     * @return string
     */
    public function getRequest()
    {
        return $this->_getData(self::REQUEST);
    }

    /**
     * Set request
     *
     * @param string $request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->setData(self::REQUEST, $request);

        return $this;
    }

    /**
     * Get request
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->_getData(self::RESPONSE);
    }

    /**
     * Set response
     *
     * @param string $response
     * @return $this
     */
    public function setResponse($response)
    {
        $this->setData(self::RESPONSE, $response);

        return $this;
    }

    /**
     * Get additional info
     *
     * @return string
     */
    public function getAdditionalInfo()
    {
        return $this->_getData(self::ADDITIONAL_INFO);
    }

    /**
     * Set additional info
     *
     * @param string $additionalInfo
     * @return $this
     */
    public function setAdditionalInfo($additionalInfo)
    {
        $this->setData(self::ADDITIONAL_INFO, $additionalInfo);

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
     * Get log types
     *
     * @return array
     */
    public function getAvailableLogTypes()
    {
        return [
            self::PING        => __('Ping'),
            self::CALCULATION => __('Calculation'),
            self::TRANSACTION => __('Transaction'),
            self::FILTER      => __('Filter'),
            self::VALIDATE    => __('Validate'),
            self::QUEUE       => __('Queue'),
        ];
    }

    /**
     * Get log levels
     *
     * @return array
     */
    public function getAvailableLogLevels()
    {
        return [
            self::LOG_LEVEL_SUCCESS => __('Success'),
            self::LOG_LEVEL_ERROR   => __('Error')
        ];
    }
}
