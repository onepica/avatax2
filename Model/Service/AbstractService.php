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
use OnePica\AvaTax\Api\ResultInterface;
use OnePica\AvaTax\Api\Service\CalculationResourceInterface;
use OnePica\AvaTax\Api\Service\CreditmemoResourceInterface;
use OnePica\AvaTax\Api\Service\InvoiceResourceInterface;
use OnePica\AvaTax\Api\Service\PingResourceInterface;
use OnePica\AvaTax\Api\Service\ValidationResourceInterface;
use OnePica\AvaTax\Api\ServiceInterface;
use OnePica\AvaTax\Model\Service\Result\BaseResult;
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
     * @var InvoiceResourceInterface
     */
    protected $invoiceResource;

    /**
     * Calculation resource
     *
     * @var CalculationResourceInterface
     */
    protected $calculationResource;

    /**
     * ping resource
     *
     * @var PingResourceInterface
     */
    protected $pingResource;

    /**
     * Creditmemo resource
     *
     * @var CreditmemoResourceInterface
     */
    protected $creditmemoResource;

    /**
     * Validation resource
     *
     * @var ValidationResourceInterface
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
     * Get Invoice Service Request Object
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return mixed
     */
    public function getInvoiceServiceRequestObject(Invoice $invoice)
    {
        if ($this->invoiceResource === null) {
            $this->invoiceResource = $this->objectManager->create($this->getInvoiceResourceClass());
            if (!$this->invoiceResource instanceof InvoiceResourceInterface) {
                throw new \LogicException('Resource must be instance of InvoiceResourceInterface.');
            }
        }

        return $this->invoiceResource->getInvoiceServiceRequestObject($invoice);
    }

    /**
     * Invoice
     *
     * @param Queue $queue
     * @return ResultInterface
     */
    public function invoice(Queue $queue)
    {
        if ($this->invoiceResource === null) {
            $this->invoiceResource = $this->objectManager->create($this->getInvoiceResourceClass());
            if (!$this->invoiceResource instanceof InvoiceResourceInterface) {
                throw new \LogicException('Resource must be instance of InvoiceResourceInterface.');
            }
        }

        return $this->invoiceResource->invoice($queue);
    }


    /**
     * Get Creditmemo Service Request Object
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return mixed
     */
    public function getCreditmemoServiceRequestObject(Creditmemo $creditmemo)
    {
        if (null === $this->creditmemoResource) {
            $this->creditmemoResource = $this->objectManager->create($this->getCreditmemoResourceClass());
            if (!$this->creditmemoResource instanceof CreditmemoResourceInterface) {
                throw new \LogicException('Resource must be instance of CreditmemoResourceInterface.');
            }
        }

        return $this->creditmemoResource->getCreditmemoServiceRequestObject($creditmemo);
    }

    /**
     * Creditmemo
     *
     * @param Queue $queue
     * @return ResultInterface
     */
    public function creditmemo(Queue $queue)
    {
        if (null === $this->creditmemoResource) {
            $this->creditmemoResource = $this->objectManager->create($this->getCreditmemoResourceClass());
            if (!$this->creditmemoResource instanceof CreditmemoResourceInterface) {
                throw new \LogicException('Resource must be instance of CreditmemoResourceInterface.');
            }
        }

        return $this->creditmemoResource->creditmemo($queue);
    }

    /**
     * Validate
     *
     * @param DataObject $object
     * @todo need to specify which object ($object) will be passed to this method
     * @return ResultInterface
     */
    public function validate($object)
    {
        if (null === $this->validationResource) {
            $this->validationResource = $this->objectManager->create($this->getValidationResourceClass());
            if (!$this->validationResource instanceof ValidationResourceInterface) {
                throw new \LogicException('Resource must be instance of ValidationResourceInterface.');
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
     * @return \OnePica\AvaTax\Api\ResultInterface
     */
    public function calculate(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        if (null === $this->calculationResource) {
            $this->calculationResource = $this->objectManager->create($this->getCalculationResourceClass());
            if (!$this->calculationResource instanceof CalculationResourceInterface) {
                throw new \LogicException('Resource must be instance of CalculationResourceInterface.');
            }
        }

        return $this->calculationResource->calculate($quote, $shippingAssignment, $total);
    }

    /**
     * Process ping
     *
     * @param \Magento\Store\Model\Store $store
     * @return BaseResult
     */
    public function ping(Store $store)
    {
        if (null === $this->pingResource) {
            $this->pingResource = $this->objectManager->create($this->getPingResourceClass());
            if (!$this->pingResource instanceof PingResourceInterface) {
                throw new \LogicException('Resource must be instance of PingResourceInterface.');
            }
        }

        return $this->pingResource->ping($store);
    }
}
