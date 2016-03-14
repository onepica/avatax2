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
namespace OnePica\AvaTax\Model\Service\Resource;

use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\Store;
use OnePica\AvaTax\Api\ConfigRepositoryInterface;
use OnePica\AvaTax\Api\Service\LoggerInterface;
use OnePica\AvaTax\Helper\Config;

/**
 * Class AbstractResource
 *
 * @package OnePica\AvaTax\Model\Service\Resource
 */
abstract class AbstractResource
{
    /**
     * Config repository
     *
     * @var \OnePica\AvaTax\Api\ConfigRepositoryInterface
     */
    protected $configRepository;

    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Service logger
     *
     * @var \OnePica\AvaTax\Api\Service\LoggerInterface
     */
    protected $logger;

    /**
     * AbstractResource constructor.
     *
     * @param \OnePica\AvaTax\Api\ConfigRepositoryInterface $configRepository
     * @param \Magento\Framework\ObjectManagerInterface     $objectManager
     * @param \OnePica\AvaTax\Helper\Config                 $config
     * @param \OnePica\AvaTax\Api\Service\LoggerInterface   $logger
     */
    public function __construct(
        ConfigRepositoryInterface $configRepository,
        ObjectManagerInterface $objectManager,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->configRepository = $configRepository;
        $this->objectManager = $objectManager;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Send request
     *
     * @param Store $store
     * @return $this
     */
    protected function send($store)
    {
        //sending request implementation
        return $this;
    }
}
