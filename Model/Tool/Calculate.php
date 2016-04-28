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
namespace Astound\AvaTax\Model\Tool;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Astound\AvaTax\Model\Service\ResolverInterface;
use Astound\AvaTax\Model\Service\Result\Calculation;
use Astound\AvaTax\Model\ServiceFactory;

/**
 * Class Calculate
 *
 * @package Astound\AvaTax\Model\Tool
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
     * @param \Astound\AvaTax\Model\Service\ResolverInterface     $resolver
     * @param \Astound\AvaTax\Model\ServiceFactory                $serviceFactory
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
