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

use OnePica\AvaTax\Model\Service\ResolverInterface;
use OnePica\AvaTax\Api\ServiceInterface;
use OnePica\AvaTax\Api\ToolInterface;
use OnePica\AvaTax\Model\ServiceFactory;

/**
 * Class AbstractTool
 *
 * @package OnePica\AvaTax\Model\Tool
 */
abstract class AbstractTool implements ToolInterface
{
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
     * @param \OnePica\AvaTax\Model\Service\ResolverInterface $resolver
     * @param \OnePica\AvaTax\Model\ServiceFactory            $serviceFactory
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
