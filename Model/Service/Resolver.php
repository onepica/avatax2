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
namespace Astound\AvaTax\Model\Service;

use Astound\AvaTax\Model\Service\ResolverInterface;
use Astound\AvaTax\Helper\Config;

/**
 * Class Resolver
 *
 * @package Astound\AvaTax\Model\Service
 */
class Resolver implements ResolverInterface
{
    /**
     * Service data
     *
     * @var array
     */
    protected $serviceData = [];

    /**
     * Config helper
     *
     * @var \Astound\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * Resolver constructor.
     *
     * @param array                         $data
     * @param \Astound\AvaTax\Helper\Config $config
     */
    public function __construct(array $data, Config $config)
    {
        $this->serviceData = $data;
        $this->config = $config;
    }

    /**
     * Get service class
     *
     * @return string
     * @throws \Exception
     */
    public function getServiceClass()
    {
        $serviceName = $this->config->getActiveService();

        if (!isset($this->serviceData[$serviceName]['service'])) {
            throw new \Exception('Service not defined.');
        }

        return $this->serviceData[$serviceName]['service'];
    }

    /**
     * Get service config class
     *
     * @return string
     * @throws \Exception
     */
    public function getServiceConfigClass()
    {
        $serviceName = $this->config->getActiveService();

        if (!isset($this->serviceData[$serviceName]['service_config'])) {
            throw new \Exception('Service config not defined.');
        }

        return $this->serviceData[$serviceName]['service_config'];
    }
}
