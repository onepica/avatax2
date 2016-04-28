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
 * @author     OnePica Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax\Model\Service;

use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\Store;

/**
 * Class ConfigFactory
 *
 * @package OnePica\AvaTax\Model\Service
 */
class ConfigFactory implements ConfigFactoryInterface
{
    /**
     * Config helper object
     *
     * @var \OnePica\AvaTax\Helper\Config
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
     * @var \OnePica\AvaTax\Model\Service\Resolver
     */
    protected $resolver;

    /**
     * ConfigFactory constructor.
     *
     * @param \OnePica\AvaTax\Model\Service\Resolver    $resolver
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
     * @return \OnePica\AvaTax\Model\Service\Avatax16\ConfigInterface
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
