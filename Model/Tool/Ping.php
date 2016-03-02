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

use Magento\Store\Model\Store;
use OnePica\AvaTax\Helper\Config as ConfigHelper;
use OnePica\AvaTax\Model\Service\Result\BaseResult;
use OnePica\AvaTax\Model\ServiceFactory;

/**
 * Class Ping
 *
 * @package OnePica\AvaTax\Model\Tool
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
     * @param \OnePica\AvaTax\Helper\Config        $config
     * @param \OnePica\AvaTax\Model\ServiceFactory $serviceFactory
     * @param \Magento\Store\Model\Store           $store
     */
    public function __construct(
        ConfigHelper $config,
        ServiceFactory $serviceFactory,
        Store $store
    ) {
        parent::__construct($config, $serviceFactory);
        $this->store = $store;
    }

    /**
     * Execute
     *
     * @return BaseResult
     */
    public function execute()
    {
        return $this->getService()->ping($this->store);
    }
}
