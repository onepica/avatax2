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
 * @author     Astound Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Plugin\Checkout\Model;

use Astound\AvaTax\Helper\Config;
use Astound\AvaTax\Model\Sales\Total\Quote\AbstractCollector;

/**
 * Class TotalsInformationManagement
 *
 * @package Astound\AvaTax\Plugin\Checkout\Model
 */
class TotalsInformationManagement
{
    /**
     * Cart repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * Message manager
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Avatax config helper
     *
     * @var Config
     */
    protected $config;

    /**
     * TotalsInformationManagement constructor.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface  $cartRepository
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param Config                                      $config
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        Config $config
    ) {
        $this->cartRepository = $cartRepository;
        $this->messageManager = $messageManager;
        $this->config = $config;
    }

    /**
     * Around calculate plugin
     *
     * @param \Magento\Checkout\Model\TotalsInformationManagement   $subject
     * @param \Closure                                              $proceed
     * @param  int                                                  $cartId
     * @param \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
     */
    public function aroundCalculate(
        \Magento\Checkout\Model\TotalsInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
    ) {
        $calculate = $proceed($cartId, $addressInformation);
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cartRepository->get($cartId);
        $store = $quote->getStore();

        if (!$this->config->isAvaTaxEnabled($store)) {
            return $calculate;
        }

        if ($quote->getData(AbstractCollector::AVATAX_ERROR)) {
            $this->messageManager->addError($this->config->getFrontendErrorMessage($store));
        }

        return $calculate;
    }
}
