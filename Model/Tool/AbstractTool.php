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
namespace Astound\AvaTax\Model\Tool;

use Astound\AvaTax\Model\Service\ResolverInterface;
use Astound\AvaTax\Api\ServiceInterface;
use Astound\AvaTax\Api\ToolInterface;
use Astound\AvaTax\Model\ServiceFactory;

/**
 * Class AbstractTool
 *
 * @package Astound\AvaTax\Model\Tool
 */
abstract class AbstractTool implements ToolInterface
{
    /**
     * Service factory
     *
     * @var \Astound\AvaTax\Model\ServiceFactory
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
     * @param \Astound\AvaTax\Model\Service\ResolverInterface $resolver
     * @param \Astound\AvaTax\Model\ServiceFactory            $serviceFactory
     */
    public function __construct(ResolverInterface $resolver, ServiceFactory $serviceFactory)
    {
        $this->serviceFactory = $serviceFactory;
        $this->service = $this->serviceFactory->create($resolver->getServiceClass());
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
