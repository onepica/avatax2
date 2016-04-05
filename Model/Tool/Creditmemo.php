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

use OnePica\AvaTax\Api\ResultInterface;
use OnePica\AvaTax\Api\Service\ResolverInterface;
use OnePica\AvaTax\Model\ServiceFactory;
use Magento\Sales\Model\Order\Creditmemo as OrderCreditmemo;
use OnePica\AvaTax\Model\Queue;

/**
 * Class Creditmemo
 *
 * @package OnePica\AvaTax\Model\Tool
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
     * Queue
     *
     * @var \OnePica\AvaTax\Model\Queue
     */
    protected $queue;

    /**
     * Creditmemo constructor.
     *
     * @param \OnePica\AvaTax\Api\Service\ResolverInterface $resolver
     * @param \OnePica\AvaTax\Model\ServiceFactory          $serviceFactory
     * @param \Magento\Sales\Model\Order\Creditmemo         $creditmemo
     */
    public function __construct(
        ResolverInterface $resolver,
        ServiceFactory $serviceFactory,
        OrderCreditmemo $creditmemo
    ) {
        parent::__construct($resolver, $serviceFactory);
        $this->init($creditmemo);
    }

    /**
     * Set creditmemo
     *
     * @param OrderCreditmemo $creditmemo
     * @return $this
     */
    public function setCreditmemo(OrderCreditmemo $creditmemo)
    {
        $this->creditmemo = $creditmemo;

        return $this;
    }

    /**
     * Set queue
     *
     * @param Queue $queue
     * @return $this
     */
    public function setQueue(Queue $queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * Get Creditmemo Service Request Object
     *
     * @return mixed
     */
    public function getCreditmemoServiceRequestObject()
    {
        return $this->getService()->getCreditmemoServiceRequestObject($this->creditmemo);
    }

    /**
     * Execute
     *
     * @return ResultInterface
     */
    public function execute()
    {
        return $this->getService()->creditmemo($this->queue);
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
