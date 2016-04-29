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
namespace Astound\AvaTax\Api\Data;

/**
 * Interface LogInterface
 *
 * @package Astound\AvaTax\Api\Data
 */
interface LogInterface
{
    /**#@+
     * Log levels
     */
    const LOG_LEVEL_SUCCESS = 'Success';
    const LOG_LEVEL_ERROR   = 'Error';
    /**#@-*/

    /**#@+
     * Log types
     */
    const PING        = 'Ping';
    const CALCULATION = 'Calculation';
    const TRANSACTION = 'Transaction';
    const FILTER      = 'Filter';
    const VALIDATE    = 'Validate';
    const QUEUE       = 'Queue';

    /**#@-*/

    /**#@+
     * Constants defined for keys of array
     */
    const LOG_ID          = 'log_id';
    const STORE_ID        = 'store_id';
    const LOG_LEVEL       = 'log_level';
    const LOG_TYPE        = 'log_type';
    const REQUEST         = 'request';
    const RESPONSE        = 'response';
    const ADDITIONAL_INFO = 'additional_info';
    const CREATED_AT      = 'created_at';
    /**#@-*/

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
     * Get log level
     *
     * @return string
     */
    public function getLogLevel();

    /**
     * Set log level
     *
     * @param string $logLevel
     * @return mixed
     */
    public function setLogLevel($logLevel);

    /**
     * Get log type
     *
     * @return string
     */
    public function getLogType();

    /**
     * Set log type
     *
     * @param string $logType
     * @return string
     */
    public function setLogType($logType);

    /**
     * Get request
     *
     * @return string
     */
    public function getRequest();

    /**
     * Set request
     *
     * @param string $request
     * @return string
     */
    public function setRequest($request);

    /**
     * Get request
     *
     * @return string
     */
    public function getResponse();

    /**
     * Set response
     *
     * @param string $response
     * @return string
     */
    public function setResponse($response);

    /**
     * Get additional info
     *
     * @return string
     */
    public function getAdditionalInfo();

    /**
     * Set additional info
     *
     * @param string $additionalInfo
     * @return string
     */
    public function setAdditionalInfo($additionalInfo);

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
}
