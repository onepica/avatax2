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
 * @author     Astound Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Model\Service;

use Astound\AvaTax\Api\LogRepositoryInterface;
use Astound\AvaTax\Model\Service\Result\ResultInterface;
use Astound\AvaTax\Api\Service\LoggerInterface;
use Astound\AvaTax\Helper\Config;
use Astound\AvaTax\Model\Log;
use Astound\AvaTax\Model\LogFactory;
use Astound\AvaTax\Model\Source\Avatax16\LogMode;

/**
 * Class Logger
 *
 * @package Astound\AvaTax\Model\Service
 */
class Logger implements LoggerInterface
{
    /**
     * Config
     *
     * @var \Astound\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * Log factory
     *
     * @var \Astound\AvaTax\Model\LogFactory
     */
    protected $logFactory;

    /**
     * Log repository
     *
     * @var \Astound\AvaTax\Api\LogRepositoryInterface
     */
    protected $logRepository;

    /**
     * Logger constructor.
     *
     * @param \Astound\AvaTax\Model\LogFactory           $logFactory
     * @param \Astound\AvaTax\Helper\Config              $config
     * @param \Astound\AvaTax\Api\LogRepositoryInterface $logRepository
     */
    public function __construct(LogFactory $logFactory, Config $config, LogRepositoryInterface $logRepository)
    {
        $this->config = $config;
        $this->logFactory = $logFactory;
        $this->logRepository = $logRepository;
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

        if ($this->config->getLogMode($store) === LogMode::ERRORS && !$result->getHasError()) {
            return $this;
        }

        $logModel = $this->logFactory->create();

        $level = $result->getHasError() ? Log::LOG_LEVEL_ERROR : Log::LOG_LEVEL_SUCCESS;

        $additional = var_export($additional, true);
        $additional = str_replace($this->config->getServiceLicenceKey($store), '[LICENSE KEY]', $additional);

        $logModel
            ->setStoreId((int)$store)
            ->setLogType($type)
            ->setLogLevel($level)
            ->setRequest(var_export($request, true))
            ->setResponse(var_export($result->getResponse(), true))
            ->setAdditionalInfo(var_export($additional, true));

        $this->logRepository->save($logModel);

        return $this;
    }
}
