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

use Magento\Customer\Model\CustomerRegistry;
use Magento\Directory\Model\RegionFactory;
use Magento\Sales\Model\Order\Address;
use Magento\Tax\Helper\Data;
use Magento\Tax\Model\ClassModelRegistry;
use Magento\Tax\Model\ResourceModel\TaxClass\Collection;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Exception\NoSuchEntityException;
use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Model\GiftWrappingHelperFactory;
use OnePica\AvaTax\Model\Source\Avatax16\CustomerCodeFormat;


/**
 * Class DataSource
 *
 * @package OnePica\AvaTax\Model\Service
 */
class DataSourceQueue extends DataSource
{
    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * DataSource constructor.
     *
     * @param \OnePica\AvaTax\Helper\Config                   $config
     * @param \Magento\Customer\Model\CustomerRegistry        $customerRegistry
     * @param \Magento\Tax\Model\ClassModelRegistry           $classModelRegistry
     * @param \Magento\Directory\Model\RegionFactory          $regionFactory
     * @param Data                                            $taxDataHelper
     * @param Collection                                      $taxClassCollection
     * @param \OnePica\AvaTax\Model\GiftWrappingHelperFactory $giftWrappingHelperFactory
     * @param GroupRepositoryInterface $groupRepository
     */
    public function __construct(
        Config $config,
        CustomerRegistry $customerRegistry,
        ClassModelRegistry $classModelRegistry,
        RegionFactory $regionFactory,
        Data $taxDataHelper,
        Collection $taxClassCollection,
        GiftWrappingHelperFactory $giftWrappingHelperFactory,
        GroupRepositoryInterface $groupRepository
    ) {
        parent::__construct($config,$customerRegistry, $classModelRegistry, $regionFactory, $taxDataHelper,
            $taxClassCollection, $giftWrappingHelperFactory);
        $this->groupRepository = $groupRepository;
    }

    /**
     * Get vat Id
     *
     * @param Store   $store
     * @param Address $address
     * @return string
     */
    public function getTaxBuyerCode($store, $address)
    {
        return (string)$address->getVatId();
    }

    /**
     * Get default buyer type
     *
     * @param Address $address
     * @return string
     */
    public function getDefaultBuyerType($address)
    {
        $groupTaxClassId = $this->groupRepository->getById($address->getOrder()->getCustomerGroupId())->getTaxClassId();
        return $this->getOpAvataxCode($groupTaxClassId);
    }

    /**
     * Get customer code
     *
     * @param Store   $store
     * @param Address $address
     * @return string
     */
    public function getCustomerCode($store, $address)
    {
        $customerId = (int)$address->getOrder()->getCustomerId();
        $customer = null;

        if ($customerId) {
            try {
                $customer = $this->customerRegistry->retrieve($customerId);
            } catch (NoSuchEntityException $e) {
                $customer = null;
            }
        }

        $customerCode = '';
        switch ($this->config->getCustomerCodeFormat($store)) {
            case CustomerCodeFormat::CUSTOMER_ID:
                $customerCode = $this->prepareCustomerId($address);
                break;
            case CustomerCodeFormat::CUSTOMER_EMAIL:
                $customerCode = $this->prepareCustomerEmail($address, $customer)
                    ?: $this->prepareCustomerId($address);
                break;
        }

        return $customerCode;
    }

    /**
     * Prepare customer id
     *
     * @param Address $address
     * @return int|string
     */
    protected function prepareCustomerId($address)
    {
        $customerId = (int)$address->getOrder()->getCustomerId();
        if (!$customerId) {
            $customerId = 'guest-' . $address->getId();
        }

        return $customerId;
    }
}
