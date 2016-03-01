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
namespace OnePica\AvaTax2\Model\Service\Resource\Avatax;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use OnePica\AvaTax2\Api\ResultInterface;
use OnePica\AvaTax2\Api\Service\CalculationResourceInterface;
use OnePica\AvaTax2\Model\Service\Resource\AbstractResource;

/**
 * Class Calculation
 *
 * @package OnePica\AvaTax2\Model\Service\Resource\Avatax
 */
class Calculation extends AbstractResource implements CalculationResourceInterface
{
    /**
     * Calculate
     *
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @return ResultInterface
     */
    public function calculate(ShippingAssignmentInterface $shippingAssignment)
    {
        // TODO: Implement calculate() method.
    }
}
