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
namespace OnePica\AvaTax\Model\Service;

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Magento\Store\Model\Store;
use OnePica\AvaTax\Model\Service\Result\ResultInterface;
use OnePica\AvaTax\Api\ServiceInterface;
use OnePica\AvaTax\Model\Service\Resource\Avatax16\Calculation;
use OnePica\AvaTax\Model\Service\Resource\Avatax16\Ping;
use OnePica\AvaTax\Model\Service\Resource\Avatax16\Queue\Creditmemo as CreditmemoResource;
use OnePica\AvaTax\Model\Service\Resource\Avatax16\Queue\Invoice as InvoiceResource;
use OnePica\AvaTax\Model\Service\Resource\Avatax16\Validation;
use OnePica\AvaTax\Model\Service\Result\Base;
use OnePica\AvaTax\Model\Queue;

/**
 * Class AbstractService
 *
 * @package OnePica\AvaTax\Model\Service
 */
abstract class AbstractService implements ServiceInterface
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManager\ObjectManager
     */
    protected $objectManager;

    /**
     * Invoice resource
     *
     * @var InvoiceResource
     */
    protected $invoiceResource;

    /**
     * Calculation resource
     *
     * @var Calculation
     */
    protected $calculationResource;

    /**
     * ping resource
     *
     * @var Ping
     */
    protected $pingResource;

    /**
     * Creditmemo resource
     *
     * @var CreditmemoResource
     */
    protected $creditmemoResource;

    /**
     * Validation resource
     *
     * @var Validation
     */
    protected $validationResource;

    /**
     * AbstractService constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get ping resource class
     *
     * @return string
     */
    abstract public function getPingResourceClass();

    /**
     * Get invoice resource class
     *
     * @return string
     */
    abstract public function getInvoiceResourceClass();

    /**
     * Get validation resource class
     *
     * @return string
     */
    abstract public function getValidationResourceClass();

    /**
     * Get creditmemo resource class
     *
     * @return string
     */
    abstract public function getCreditmemoResourceClass();

    /**
     * Get calculation resource class
     *
     * @return string
     */
    abstract public function getCalculationResourceClass();

    /**
     * Submit
     *
     * @param Queue $queue
     *
     * @return ResultInterface
     */
    public function submit(Queue $queue)
    {
        return $this->getQueueResource($queue->getType())->submit($queue);
    }

    /**
     * Get Invoice Service Request Object
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     *
     * @return mixed
     */
    public function getInvoiceServiceRequestObject(Invoice $invoice)
    {
        return $this->getQueueResource(Queue::TYPE_INVOICE)->getServiceRequestObject($invoice);
    }

    /**
     * Get Creditmemo Service Request Object
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     *
     * @return mixed
     */
    public function getCreditmemoServiceRequestObject(Creditmemo $creditmemo)
    {
        return $this->getQueueResource(Queue::TYPE_CREDITMEMO)->getServiceRequestObject($creditmemo);
    }

    /**
     * Get queue resource object
     *
     * @param $type
     *
     * @return InvoiceResource|CreditmemoResource
     */
    protected function getQueueResource($type)
    {
        if ($type === Queue::TYPE_INVOICE) {
            return $this->objectManager->create($this->getInvoiceResourceClass());
        } elseif ($type === Queue::TYPE_CREDITMEMO) {
            return $this->objectManager->create($this->getCreditmemoResourceClass());
        } else {
            throw new \LogicException('Wrong Queue type.');
        }
    }

    /**
     * Validate
     *
     * @param \OnePica\AvaTax\Model\Service\Request\Address $object
     *
     * @return ResultInterface
     */
    public function validate($object)
    {
        if (null === $this->validationResource) {
            $this->validationResource = $this->objectManager->create($this->getValidationResourceClass());
            if (!$this->validationResource instanceof Validation) {
                throw new \LogicException('Resource must be instance of ValidationResource.');
            }
        }

        return $this->validationResource->validate($object);
    }

    /**
     * Calculate
     *
     * @param \Magento\Quote\Model\Quote                          $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total            $total
     *
     * @return \OnePica\AvaTax\Model\Service\Result\ResultInterface
     */
    public function calculate(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        if (null === $this->calculationResource) {
            $this->calculationResource = $this->objectManager->create($this->getCalculationResourceClass());
            if (!$this->calculationResource instanceof Calculation) {
                throw new \LogicException('Resource must be instance of CalculationResource.');
            }
        }

        return $this->calculationResource->calculate($quote, $shippingAssignment, $total);
    }

    /**
     * Process ping
     *
     * @param \Magento\Store\Model\Store $store
     *
     * @return Base
     */
    public function ping(Store $store)
    {
        if (null === $this->pingResource) {
            $this->pingResource = $this->objectManager->create($this->getPingResourceClass());
            if (!$this->pingResource instanceof Ping) {
                throw new \LogicException('Resource must be instance of PingResource.');
            }
        }

        return $this->pingResource->ping($store);
    }
}
