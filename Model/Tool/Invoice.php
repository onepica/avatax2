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

use OnePica\AvaTax\Api\ResultInterface;
use OnePica\AvaTax\Helper\Config as ConfigHelper;
use OnePica\AvaTax\Model\ServiceFactory;
use Magento\Sales\Model\Order\Invoice as OrderInvoice;

/**
 * Class Invoice
 *
 * @package OnePica\AvaTax\Model\Tool
 */
class Invoice extends AbstractTool
{
    /**
     * Invoice
     *
     * @var \Magento\Sales\Model\Order\Invoice
     */
    protected $invoice;

    /**
     * Invoice constructor.
     *
     * @param \OnePica\AvaTax\Helper\Config        $config
     * @param \OnePica\AvaTax\Model\ServiceFactory $serviceFactory
     * @param \Magento\Sales\Model\Order\Invoice   $invoice
     */
    public function __construct(
        ConfigHelper $config,
        ServiceFactory $serviceFactory,
        OrderInvoice $invoice
    ) {
        parent::__construct($config, $serviceFactory);
        $this->init($invoice);
    }

    /**
     * Execute
     *
     * @return ResultInterface
     */
    public function execute()
    {
        return $this->getService()->invoice($this->invoice);
    }

    /**
     * Init tool
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function init(OrderInvoice $invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }
}
