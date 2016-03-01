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
namespace OnePica\AvaTax2\Model\Service;

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManager\ObjectManager;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Magento\Store\Model\Store;
use OnePica\AvaTax2\Api\ResultInterface;
use OnePica\AvaTax2\Api\Service\CalculationResourceInterface;
use OnePica\AvaTax2\Api\Service\CreditmemoResourceInterface;
use OnePica\AvaTax2\Api\Service\InvoiceResourceInterface;
use OnePica\AvaTax2\Api\Service\PingResourceInterface;
use OnePica\AvaTax2\Api\Service\ValidationResourceInterface;
use OnePica\AvaTax2\Api\ServiceInterface;
use OnePica\AvaTax2\Model\Service\Result\BaseResult;

/**
 * Class AbstractService
 *
 * @package OnePica\AvaTax2\Model\Service
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
     * @param \Magento\Framework\ObjectManager\ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
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
     * Invoice
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return ResultInterface
     */
    public function invoice(Invoice $invoice)
    {
        if ($this->invoiceResource === null) {
            $this->invoiceResource = $this->objectManager->create($this->getInvoiceResourceClass());
            if (!$this->invoiceResource instanceof InvoiceResourceInterface) {
                throw new \LogicException('Resource must be instance of InvoiceResourceInterface.');
            }
        }

        return $this->invoiceResource->invoice($invoice);
    }

    /**
     * Creditmemo
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return ResultInterface
     */
    public function creditmemo(Creditmemo $creditmemo)
    {
        if (null === $this->creditmemoResource) {
            $this->creditmemoResource = $this->objectManager->create($this->getCreditmemoResourceClass());
            if (!$this->creditmemoResource instanceof CreditmemoResourceInterface) {
                throw new \LogicException('Resource must be instance of CreditmemoResourceInterface.');
            }
        }

        return $this->creditmemoResource->creditmemo($creditmemo);
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
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @return ResultInterface
     */
    public function calculate(ShippingAssignmentInterface $shippingAssignment)
    {
        if (null === $this->calculationResource) {
            $this->calculationResource = $this->objectManager->create($this->getCalculationResourceClass());
            if (!$this->calculationResource instanceof CalculationResourceInterface) {
                throw new \LogicException('Resource must be instance of CalculationResourceInterface.');
            }
        }

        return $this->calculationResource->calculate($shippingAssignment);
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
