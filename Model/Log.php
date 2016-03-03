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
use OnePica\AvaTax\Api\Data\LogInterface;
use OnePica\AvaTax\Model\ResourceModel\Log as LogResource;
use OnePica\AvaTax\Model\ResourceModel\Log\Collection;

/**
 * Class Log
 *
 * @method Collection getCollection()
 * @method LogResource getResource()
 * @method $this save()
 *
 * @package OnePica\AvaTax\Model
 */
class Log extends AbstractModel implements LogInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'onepica_avatax_log';

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
     * @return mixed
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
     * @return mixed
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
     * @return string
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
     * @return string
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
     * @return string
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
     * @return string
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
     * @return string
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(self::CREATED_AT, $createdAt);

        return $this;
    }
}
