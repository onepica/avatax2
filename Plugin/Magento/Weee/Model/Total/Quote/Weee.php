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

namespace OnePica\AvaTax\Plugin\Magento\Weee\Model\Total\Quote;

use OnePica\AvaTax\Helper\Config;

/**
 * Class Weee
 *
 * @package OnePica\AvaTax\Plugin\Magento\Weee\Model\Total\Quote
 */
class Weee
{
    /**
     * Avatax Config helper
     *
     * @var Config
     */
    protected $config;

    /**
     * Weee constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Around collect
     *
     * @param \Magento\Weee\Model\Total\Quote\Weee                $subject
     * @param \Closure                                            $proceed
     * @param \Magento\Quote\Model\Quote                          $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total            $total
     *
     * @return \Magento\Checkout\Model\ShippingInformationManagement
     */
    public function aroundCollect(
        \Magento\Weee\Model\Total\Quote\Weee $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        if ($this->config->isAvaTaxEnabled($quote->getStore())) {
            return $subject;
        }

        return $proceed($quote, $shippingAssignment, $total);
    }
}
