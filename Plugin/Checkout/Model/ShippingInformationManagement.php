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
namespace OnePica\AvaTax\Plugin\Checkout\Model;

use Magento\Quote\Api\CartRepositoryInterface;
use OnePica\AvaTax\Helper\Config;

/**
 * Class ShippingInformationManagement
 */
class ShippingInformationManagement
{
    /**
     * @var Config
     */
    protected $config = null;

    /**
     * ShippingInformationManagement constructor
     *
     * @param Config $config
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        Config $config,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->config = $config;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Save Address Information
     *
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param \Closure $proceed
     * @param $cartId,
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function aroundSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $quote = $this->quoteRepository->getActive($cartId);
        $storeId = $quote->getStoreId();
        $this->validateAndNormalize($storeId, $addressInformation);
        $paymentInformation = $proceed($cartId, $addressInformation);
        return $paymentInformation;
    }

    /**
     * Validate and normalize
     *
     * @param int $storeId,
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    protected function validateAndNormalize(
        $storeId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {

        $shippingAddress = $addressInformation->getShippingAddress();
        $validationResult = $shippingAddress->validate();

        if ($validationResult !== true) {
            $this->showErrorsAndStopCheckout($validationResult);
        }
    }

    /**
     * Show errors and stop checkout
     *
     * @param array $errors
     * @throws \Exception
     */
    protected function showErrorsAndStopCheckout(array $errors)
    {
        throw new \Exception(implode(', ', $errors));
    }
}
