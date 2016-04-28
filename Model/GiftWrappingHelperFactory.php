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
 * @author     Astound Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Model;

use Magento\Framework\ObjectManagerInterface;
use Astound\AvaTax\Helper\Config;

/**
 * Class GiftWrappingHelperFactory
 *
 * @package Astound\AvaTax\Model
 */
class GiftWrappingHelperFactory
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Config helper
     *
     * @var \Astound\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * GiftWrappingHelperFactory constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Astound\AvaTax\Helper\Config             $config
     */
    public function __construct(ObjectManagerInterface $objectManager, Config $config)
    {
        $this->objectManager = $objectManager;
        $this->config = $config;
    }

    /**
     * Create giftwrapping data helper
     *
     * @param array $data
     * @return \Magento\GiftWrapping\Helper\Data|null
     */
    public function create($data = [])
    {
        if ($this->config->getMagentoEdition() === 'Enterprise') {
            return $this->objectManager->create('Magento\GiftWrapping\Helper\Data', $data);
        }

        return null;
    }
}
