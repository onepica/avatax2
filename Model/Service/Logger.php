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

use OnePica\AvaTax\Api\LogRepositoryInterface;
use OnePica\AvaTax\Model\Service\Result\ResultInterface;
use OnePica\AvaTax\Api\Service\LoggerInterface;
use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Model\Log;
use OnePica\AvaTax\Model\LogFactory;
use OnePica\AvaTax\Model\Source\Avatax16\LogMode;

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
     * Log repository
     *
     * @var \OnePica\AvaTax\Api\LogRepositoryInterface
     */
    protected $logRepository;

    /**
     * Logger constructor.
     *
     * @param \OnePica\AvaTax\Model\LogFactory           $logFactory
     * @param \OnePica\AvaTax\Helper\Config              $config
     * @param \OnePica\AvaTax\Api\LogRepositoryInterface $logRepository
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
