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
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use OnePica\AvaTax\Api\Service\ResolverInterface;
use OnePica\AvaTax\Model\Service\Result\Calculation;
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
     * Total model
     *
     * @var \Magento\Quote\Model\Quote\Address\Total
     */
    protected $total;

    /**
     * Quote model
     *
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * Calculate constructor.
     *
     * @param \OnePica\AvaTax\Api\Service\ResolverInterface       $resolver
     * @param \OnePica\AvaTax\Model\ServiceFactory                $serviceFactory
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote                          $quote
     * @param \Magento\Quote\Model\Quote\Address\Total            $total
     */
    public function __construct(
        ResolverInterface $resolver,
        ServiceFactory $serviceFactory,
        ShippingAssignmentInterface $shippingAssignment,
        Quote $quote,
        Total $total
    ) {
        parent::__construct($resolver, $serviceFactory);
        $this->shippingAssignment = $shippingAssignment;
        $this->total = $total;
        $this->quote = $quote;
    }

    /**
     * Execute
     *
     * @return Calculation
     */
    public function execute()
    {
        return $this->getService()->calculate($this->quote, $this->shippingAssignment, $this->total);
    }
}
