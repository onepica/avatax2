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

use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\Store;

/**
 * Class ConfigFactory
 *
 * @package Astound\AvaTax\Model\Service
 */
class ConfigFactory implements ConfigFactoryInterface
{
    /**
     * Config helper object
     *
     * @var \Astound\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Service resolver
     *
     * @var \Astound\AvaTax\Model\Service\Resolver
     */
    protected $resolver;

    /**
     * ConfigFactory constructor.
     *
     * @param \Astound\AvaTax\Model\Service\Resolver    $resolver
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(Resolver $resolver, ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->resolver = $resolver;
    }

    /**
     * Create config object
     *
     * @param \Magento\Store\Model\Store $store
     * @return \Astound\AvaTax\Model\Service\Avatax16\ConfigInterface
     */
    public function create(Store $store)
    {
        return $this->objectManager->create($this->getConfigClass(), ['store' => $store]);
    }

    /**
     * Get config class name
     *
     * @return string
     */
    protected function getConfigClass()
    {
        return $this->resolver->getServiceConfigClass();
    }
}
