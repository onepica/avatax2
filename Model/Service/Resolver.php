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

use OnePica\AvaTax\Api\Service\ResolverInterface;
use OnePica\AvaTax\Helper\Config;

/**
 * Class Resolver
 *
 * @package OnePica\AvaTax\Model\Service
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
     * @var \OnePica\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * Resolver constructor.
     *
     * @param array                         $data
     * @param \OnePica\AvaTax\Helper\Config $config
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
     */
    public function getServiceClass()
    {
        $serviceName = $this->config->getActiveService();

        return isset($this->serviceData[$serviceName]['service'])
            ? $this->serviceData[$serviceName]['service']
            : '';
    }

    /**
     * Get service config class
     *
     * @return string
     */
    public function getServiceConfigClass()
    {
        $serviceName = $this->config->getActiveService();

        return isset($this->serviceData[$serviceName]['service_config'])
            ? $this->serviceData[$serviceName]['service_config']
            : '';
    }
}
