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
 * @author     Astound Codemaster <codemaster@astoundcommerce.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Model\Sales\Total\Quote;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Tax\Helper\Data as TaxDataHelper;
use Astound\AvaTax\Helper\Address;
use Astound\AvaTax\Helper\Config;
use Astound\AvaTax\Helper\Data as AvataxDataHelper;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total as AddressTotal;
use Astound\AvaTax\Model\Service\Result\Calculation;
use Astound\AvaTax\Model\Tool\Calculate;

/**
 * Class AbstractCollector
 *
 * @package Astound\AvaTax\Model\Sales\Total\Quote
 */
abstract class AbstractCollector extends AbstractTotal
{
    /**
     * Avatax error quote key
     */
    const AVATAX_ERROR = 'avatax_error';

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
     * @var \Astound\AvaTax\Helper\Config
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
     * @param \Astound\AvaTax\Helper\Config                     $config
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

        if ($result !== null && $result->getHasError() && !$quote->getData(self::AVATAX_ERROR)) {
            $quote->setData(self::AVATAX_ERROR, true);
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
            || !$this->addressHelper->isAddressActionable(
                $address,
                $quote->getStore(),
                AvataxDataHelper::REGION_FILTER_MODE_TAX
            )
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
