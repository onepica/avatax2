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
namespace OnePica\AvaTax\Model\Service\Resource\Avatax16\Queue;

use OnePica\AvaTax\Api\ResultInterface;
use OnePica\AvaTax\Api\Service\CreditmemoResourceInterface;
use OnePica\AvaTax\Model\Queue;

/**
 * Class Creditmemo
 *
 * @package OnePica\AvaTax\Model\Service\Resource\Avatax\Queue
 */
class Creditmemo extends AbstractQueue implements CreditmemoResourceInterface
{
    /**
     * Get Creditmemo Service Request Object
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return mixed
     */
    public function getCreditmemoServiceRequestObject(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        // TODO: Implement getCreditmemoServiceRequestObject() method.
        return array();
    }

    /**
     * Creditmemo
     *
     * @param Queue $queue
     * @return ResultInterface
     */
    public function creditmemo(Queue $queue)
    {
        // TODO: Implement creditmemo() method.
    }
}
