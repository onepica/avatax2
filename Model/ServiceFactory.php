<?php
/**
 * OnePica_AvaTax2
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
namespace OnePica\AvaTax2\Model;

use Magento\Framework\ObjectManagerInterface;
use OnePica\AvaTax2\Api\ServiceInterface;

/**
 * Class ServiceFactory
 *
 * @package OnePica\AvaTax2\Model
 */
class ServiceFactory
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManager\ObjectManager
     */
    private $objectManager;

    /**
     * ServiceFactory constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create service
     *
     * @param string $service
     * @return ServiceInterface
     */
    public function create($service)
    {
        return $this->objectManager->get($service);
    }
}
