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

use Magento\Store\Model\Store;
use Astound\AvaTax\Model\Service\ResolverInterface;
use Astound\AvaTax\Model\Service\Result\Base;
use Astound\AvaTax\Model\ServiceFactory;

/**
 * Class Ping
 *
 * @package Astound\AvaTax\Model\Tool
 */
class Ping extends AbstractTool
{
    /**
     * Store
     *
     * @var \Magento\Store\Model\Store
     */
    protected $store;

    /**
     * Ping constructor.
     *
     * @param \Astound\AvaTax\Model\Service\ResolverInterface $resolver
     * @param \Astound\AvaTax\Model\ServiceFactory            $serviceFactory
     * @param \Magento\Store\Model\Store                      $store
     */
    public function __construct(
        ResolverInterface $resolver,
        ServiceFactory $serviceFactory,
        Store $store
    ) {
        parent::__construct($resolver, $serviceFactory);
        $this->store = $store;
    }

    /**
     * Execute
     *
     * @return Base
     */
    public function execute()
    {
        return $this->getService()->ping($this->store);
    }
}
