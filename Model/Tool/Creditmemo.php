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
namespace OnePica\AvaTax2\Model\Tool;

use OnePica\AvaTax2\Api\ResultInterface;
use OnePica\AvaTax2\Helper\Config as ConfigHelper;
use OnePica\AvaTax2\Model\ServiceFactory;
use Magento\Sales\Model\Order\Creditmemo as OrderCreditmemo;

/**
 * Class Creditmemo
 *
 * @package OnePica\AvaTax2\Model\Tool
 */
class Creditmemo extends AbstractTool
{
    /**
     * Creditmemo
     *
     * @var \Magento\Sales\Model\Order\Creditmemo
     */
    protected $creditmemo;

    /**
     * Creditmemo constructor.
     *
     * @param \OnePica\AvaTax2\Helper\Config        $config
     * @param \OnePica\AvaTax2\Model\ServiceFactory $serviceFactory
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     */
    public function __construct(
        ConfigHelper $config,
        ServiceFactory $serviceFactory,
        OrderCreditmemo $creditmemo
    ) {
        parent::__construct($config, $serviceFactory);
        $this->init($creditmemo);
    }

    /**
     * Execute
     *
     * @return ResultInterface
     */
    public function execute()
    {
        return $this->getService()->creditmemo($this->creditmemo);
    }

    /**
     * Init tool
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    protected function init(OrderCreditmemo $creditmemo)
    {
        $this->creditmemo = $creditmemo;

        return $this;
    }
}
