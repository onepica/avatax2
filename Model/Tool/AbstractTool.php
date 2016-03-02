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
namespace OnePica\AvaTax\Model\Tool;

use OnePica\AvaTax\Api\ServiceInterface;
use OnePica\AvaTax\Api\ToolInterface;
use OnePica\AvaTax\Helper\Config as ConfigHelper;
use OnePica\AvaTax\Model\ServiceFactory;

/**
 * Class AbstractTool
 *
 * @package OnePica\AvaTax\Model\Tool
 */
abstract class AbstractTool implements ToolInterface
{
    /**
     * Config helper
     *
     * @var \OnePica\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * Service factory
     *
     * @var \OnePica\AvaTax\Model\ServiceFactory
     */
    protected $serviceFactory;

    /**
     * Service
     *
     * @var ServiceInterface
     */
    protected $service;

    /**
     * AbstractTool constructor.
     *
     * @param \OnePica\AvaTax\Helper\Config         $config
     * @param \OnePica\AvaTax\Model\ServiceFactory $serviceFactory
     */
    public function __construct(ConfigHelper $config, ServiceFactory $serviceFactory)
    {
        $this->config = $config;
        $this->serviceFactory = $serviceFactory;
        $this->service = $this->serviceFactory->create($this->config->getActiveService());
    }

    /**
     * Get service
     *
     * @return ServiceInterface
     */
    protected function getService()
    {
        return $this->service;
    }
}
