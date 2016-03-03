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

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use OnePica\AvaTax\Api\ResultInterface;
use OnePica\AvaTax\Api\Service\ResolverInterface;
use OnePica\AvaTax\Model\ServiceFactory;

/**
 * Class Calculate
 *
 * @package OnePica\AvaTax\Model\Tool
 */
class Calculate extends AbstractTool
{
    /**
     * Shipping assignment
     *
     * @var \Magento\Quote\Api\Data\ShippingAssignmentInterface
     */
    protected $shippingAssignment;

    /**
     * Calculate constructor.
     *
     * @param \OnePica\AvaTax\Api\Service\ResolverInterface       $resolver
     * @param \OnePica\AvaTax\Model\ServiceFactory                $serviceFactory
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     */
    public function __construct(
        ResolverInterface $resolver,
        ServiceFactory $serviceFactory,
        ShippingAssignmentInterface $shippingAssignment
    ) {
        parent::__construct($resolver, $serviceFactory);
        $this->shippingAssignment = $shippingAssignment;
    }

    /**
     * Execute
     *
     * @return ResultInterface
     */
    public function execute()
    {
        return $this->getService()->calculate($this->shippingAssignment);
    }
}
