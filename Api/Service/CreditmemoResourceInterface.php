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
namespace OnePica\AvaTax\Api\Service;

use Magento\Sales\Model\Order\Creditmemo;
use OnePica\AvaTax\Api\ResultInterface;
use OnePica\AvaTax\Model\Queue;

/**
 * Class CreditmemoResourceInterface
 *
 * @package OnePica\AvaTax\Api\Service
 */
interface CreditmemoResourceInterface
{
    /**
     * Get Creditmemo Service Request Object
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return mixed
     */
    public function getCreditmemoServiceRequestObject(\Magento\Sales\Model\Order\Creditmemo $creditmemo);

    /**
     * Queue submit
     * Send request object to service
     *
     * @param Queue $queue
     * @return ResultInterface
     */
    public function submit(Queue $queue);
}
