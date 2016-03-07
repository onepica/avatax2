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
namespace OnePica\AvaTax\Model\Service;

use OnePica\AvaTax\Api\ResultInterface;
use OnePica\AvaTax\Api\Service\LoggerInterface;
use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Model\Log;
use OnePica\AvaTax\Model\LogFactory;

/**
 * Class Logger
 *
 * @package OnePica\AvaTax\Model\Service
 */
class Logger implements LoggerInterface
{
    /**
     * Config
     *
     * @var \OnePica\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * Log factory
     *
     * @var \OnePica\AvaTax\Model\LogFactory
     */
    protected $logFactory;

    /**
     * Logger constructor.
     *
     * @param \OnePica\AvaTax\Model\LogFactory $logFactory
     * @param \OnePica\AvaTax\Helper\Config    $config
     */
    public function __construct(LogFactory $logFactory, Config $config)
    {
        $this->config = $config;
        $this->logFactory = $logFactory;
    }

    /**
     * Log service data
     *
     * @param string          $type
     * @param mixed           $request
     * @param ResultInterface $result
     * @param null|int        $store
     * @param mixed           $additional
     * @return $this
     */
    public function log($type, $request, ResultInterface $result, $store = null, $additional = null)
    {
        if (!in_array($type, $this->config->getAllowedLogTypes($store))) {
            return $this;
        }

        $logModel = $this->logFactory->create();

        $level = $result->getHasError() ? Log::LOG_LEVEL_ERROR : Log::LOG_LEVEL_SUCCESS;

        $logModel
            ->setStoreId((int)$store)
            ->setLogType($type)
            ->setLogLevel($level)
            ->setRequest(var_export($request, true))
            ->setResponse(var_export($result->getResponse(), true))
            ->setAdditionalInfo(var_export($additional, true))
            ->save();

        return $this;
    }
}
