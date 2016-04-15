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
namespace OnePica\AvaTax\Model\Sales\Total\Quote;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Tax\Helper\Data as TaxDataHelper;
use OnePica\AvaTax\Helper\Address;
use OnePica\AvaTax\Helper\Config;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total as AddressTotal;
use OnePica\AvaTax\Model\Service\Result\Calculation;
use OnePica\AvaTax\Model\Tool\Calculate;

/**
 * Class AbstractCollector
 *
 * @package OnePica\AvaTax\Model\Sales\Total\Quote
 */
abstract class AbstractCollector extends AbstractTotal
{
    /**
     * Calculate tool registry key pattern
     */
    const CALCULATE_RESULT_KEY_PATTERN = 'avatax_calculate_result_%s';

    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Price Currency
     *
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Tax data helper
     *
     * @var TaxDataHelper
     */
    protected $taxDataHelper;

    /**
     * Config
     *
     * @var \OnePica\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Need collect tax flag
     *
     * @var bool
     */
    protected $needCollect = true;

    /**
     * Address helper
     *
     *
     * @var Address
     */
    protected $addressHelper;

    /**
     * AbstractCollector constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface         $objectManager
     * @param \Magento\Framework\Registry                       $registry
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Tax\Helper\Data                          $taxDataHelper
     * @param \OnePica\AvaTax\Helper\Config                     $config
     * @param Address                                           $addressHelper
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        PriceCurrencyInterface $priceCurrency,
        TaxDataHelper $taxDataHelper,
        Config $config,
        Address $addressHelper
    ) {
        $this->objectManager = $objectManager;
        $this->priceCurrency = $priceCurrency;
        $this->taxDataHelper = $taxDataHelper;
        $this->config = $config;
        $this->registry = $registry;
        $this->addressHelper = $addressHelper;
    }

    /**
     * Collect
     *
     * @param \Magento\Quote\Model\Quote                          $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total            $total
     * @return $this
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        if ($this->isFiltered($quote, $shippingAssignment)) {
            return $this;
        }

        $result = $this->getCalculationResult($quote, $shippingAssignment, $total);

        if ($result !== null && $result->getHasError() && !$quote->getData('avatax_error')) {
            $quote->setData('avatax_error', true);
        }

        return $this;
    }

    /**
     * Get calculate tool
     *
     * @param Quote                       $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param AddressTotal                $total
     * @return Calculation
     */
    protected function getCalculationResult(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        AddressTotal $total
    ) {
        $result = $this->registry->registry($this->getRegistryKey($shippingAssignment));
        if ($result === null) {
            $result = $this->objectManager->create(
                Calculate::class,
                [
                    'shippingAssignment' => $shippingAssignment,
                    'quote'              => $quote,
                    'total'              => $total
                ]
            )->execute();

            $this->registry->register($this->getRegistryKey($shippingAssignment), $result);
        }

        return $result;
    }

    /**
     * Is filtered
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @return bool
     */
    protected function isFiltered(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
    ) {
        $address = $shippingAssignment->getShipping()->getAddress();

        if (!$address->getPostcode()
            || !$shippingAssignment->getItems()
            || !$this->addressHelper->isAddressActionable($address, $quote->getStore())
            || !$shippingAssignment->getShipping()->getAddress()->getId()

        ) {
            return true;
        }

        return false;
    }

    /**
     * Get registry key
     *
     * @param ShippingAssignmentInterface $shippingAssignment
     * @return string
     */
    protected function getRegistryKey(ShippingAssignmentInterface $shippingAssignment)
    {
        $addressId = $shippingAssignment->getShipping()->getAddress()->getId();

        return sprintf(self::CALCULATE_RESULT_KEY_PATTERN, $addressId);
    }
}
